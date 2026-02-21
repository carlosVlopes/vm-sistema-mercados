<?php

namespace App\Jobs;

use App\Models\Calculation;
use App\Models\Sale;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SyncSalesJob implements ShouldQueue
{
    use Queueable;

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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $calc = Calculation::find($this->calc_id);

        if (!$calc) {
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

        $data = $response->json();

        \Log::info($this->page);
        \Log::info(count($data));

        foreach ($data as $sale) 
        {
            Sale::upsertFromApi($sale, $this->client_id, $this->condominium_id, $this->user_id);
        }

        $hasMore = count($data) === 300;

        if(!$hasMore)
        { 
            $total = Sale::period(
                client_id: $this->client_id,
                condominium_id: $this->condominium_id,
                period_start: $this->day . ' 00:00:00',
                period_end: $this->day . ' 23:59:59',
            )->sum('value'); 

            $calc->increment('total', $total);

            \Log::info('Total atualizado: ' . $calc->fresh()->total);

            $calc->update([ 'status' => 'done', 'progress' => 100]); 

            return; 
        }
        
        $nextPage = $this->page + 1;

        SyncSalesJob::dispatch(
            $calc->id,
            $this->clientId,
            $this->condominiumId,
            $this->api_token,
            $nextPage,
            $this->day
        );
    }
}
