<?php

namespace App\Jobs;

use App\Models\Calculation;
use App\Models\Sale;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncSalesJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [30, 60, 120];

    public $maxExceptions = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $calc_id,
        protected int $client_id,
        protected int $condominium_id,
        public string $api_token,
        public int $user_id,
        protected int $page,
        protected string $day
    ) {}

    public function failed(\Throwable $exception): void
    {
        Log::error('SyncSalesJob falhou definitivamente', [
            'calc_id' => $this->calc_id,
            'day' => $this->day,
            'page' => $this->page,
            'error' => $exception->getMessage(),
        ]);

        $calc = Calculation::find($this->calc_id);

        if ($calc) {
            $calc->update(['status' => 'error']);
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $calc = Calculation::find($this->calc_id);

        if (!$calc || $calc->status === 'error') {
            return;
        }

        $calc->update(['status' => 'processing']);

        $response = Http::timeout(120)->retry(3, 5000)->get('https://vmpay.vertitecnologia.com.br/api/v1/cashless_sales', [
            'access_token' => $this->api_token,
            'client_id'    => $this->condominium_id,
            'start_date'   => $this->day . ' 00:00:00',
            'end_date'     => $this->day . ' 23:59:59',
            'per_page'     => 300,
            'page'         => $this->page,
        ]);

        if (!$response->successful()) {
            Log::warning('SyncSalesJob: API retornou erro HTTP', [
                'calc_id' => $this->calc_id,
                'day' => $this->day,
                'page' => $this->page,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException(
                "API VM-PAY retornou HTTP {$response->status()} para dia {$this->day}, página {$this->page}"
            );
        }

        $data = $response->json();

        if (!is_array($data)) {
            Log::warning('SyncSalesJob: resposta da API não é um array válido', [
                'calc_id' => $this->calc_id,
                'day' => $this->day,
                'page' => $this->page,
                'response_type' => gettype($data),
            ]);

            throw new \RuntimeException(
                "API VM-PAY retornou resposta inválida para dia {$this->day}, página {$this->page}"
            );
        }

        // Filtrar apenas vendas com estrutura válida
        $validSales = [];
        $dayTotal = 0;

        foreach ($data as $index => $sale) {
            if (!isset($sale['id'])) {
                Log::warning('SyncSalesJob: venda sem campo id, ignorando', [
                    'calc_id' => $this->calc_id,
                    'day' => $this->day,
                    'index' => $index,
                ]);
                continue;
            }

            $validSales[] = $sale;
            $dayTotal += (float) ($sale['value'] ?? 0);
        }

        if (!empty($validSales)) {
            Sale::upsertBulkFromApi($validSales, $this->client_id, $this->condominium_id, $this->user_id);
        }

        $hasMore = count($data) === 300;

        if (!$hasMore) {
            $calc->increment('processed_days');
            $calc->increment('total', $dayTotal);

            $calc->refresh();

            $progress = $calc->total_days > 0
                ? intval(($calc->processed_days / $calc->total_days) * 100)
                : 0;

            $calc->update([
                'progress' => $progress,
                'status' => $progress >= 100 ? 'done' : 'processing',
            ]);

            return;
        }

        Log::info('SyncSalesJob: mais de 300 vendas, buscando próxima página', [
            'day' => $this->day,
            'next_page' => $this->page + 1,
        ]);

        SyncSalesJob::dispatch(
            $this->calc_id,
            $this->client_id,
            $this->condominium_id,
            $this->api_token,
            $this->user_id,
            $this->page + 1,
            $this->day
        );
    }
}
