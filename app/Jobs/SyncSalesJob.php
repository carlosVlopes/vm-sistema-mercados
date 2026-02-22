<?php

namespace App\Jobs;

use App\Models\Calculation;
use App\Models\Sale;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
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

        $dayTotal = 0;
    
        foreach ($data as $sale) 
        {
            Sale::upsertFromApi($sale, $this->client_id, $this->condominium_id, $this->user_id);
            $dayTotal += $sale['value'];
        }

        $hasMore = count($data) === 300;

        if(!$hasMore)
        { 
            $calc->update([
                'processed_days' => DB::raw('processed_days + 1'),
                'total' => DB::raw("total + {$dayTotal}"),
            ]);

            $calc->refresh();

            if ($calc->total_days > 0) {
                $progress = intval(($calc->processed_days / $calc->total_days) * 100);
            } else {
                $progress = 0;
            }

            $calc->update([
                'progress' => $progress,
                'status' => $progress >= 100 ? 'done' : 'processing',
            ]);

            return; 
        }
        
        $nextPage = $this->page + 1;

        SyncSalesJob::dispatch(
            $calc->id,
            $this->client_id,
            $this->condominium_id,
            $this->api_token,
            $nextPage,
            $this->day
        );
    }
}
