<?php

namespace App\Filament\Resources\Transfers;

use App\Filament\Resources\Transfers\Pages\CreateTransfer;
use App\Filament\Resources\Transfers\Pages\EditTransfer;
use App\Filament\Resources\Transfers\Pages\ListTransfers;
use App\Filament\Resources\Transfers\Schemas\TransferForm;
use App\Filament\Resources\Transfers\Tables\TransfersTable;
use App\Models\Transfer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Brick\Money\Money;
use Brick\Money\Currency;
use Brick\Math\RoundingMode;
use Brick\Money\Formatter\MoneyNumberFormatter;
use NumberFormatter;


class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Repasses';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'repasses';

    protected static ?string $modelLabel = 'repasse'; // texto do botao/inserir/edita

    public static function form(Schema $schema): Schema
    {
        return TransferForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransfersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransfers::route('/'),
            'create' => CreateTransfer::route('/create'),
            'edit' => EditTransfer::route('/{record}/edit'),
        ];
    }

    public static function get_client_condominiums($client_id)
    {
        $apiData = Cache::remember('vm_clients_api', 600, function () {

            $response = Http::get('https://vmpay.vertitecnologia.com.br/api/v1/clients', [
                'access_token' => env('VM_API_TOKEN'),
            ]);

            return $response->json();
        });

        $query = \DB::table('clients_condominiums')->where('client_id', '=', $client_id);

        $condominiums_ids = $query
            ->pluck('condominium_id')
            ->toArray();

        $ids = array_flip($condominiums_ids);

        return collect($apiData)
            ->filter(fn ($item) => isset($ids[$item['id']]))
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function fetch_sales($get)
    {
        $clientId = $get('client_id');
        $condominiumId = $get('condominium_id');
        $periodStart = $get('period_start');
        $periodEnd = date('Y-m-d 23:59:59', strtotime($get('period_end')));

        if (! $clientId || ! $condominiumId || ! $periodStart || ! $periodEnd) {
            return [];
        }

        $sales_value = 0;
        $page = 1;

        do {

            $response = Http::get('https://vmpay.vertitecnologia.com.br/api/v1/vends', [
                'access_token' => env('VM_API_TOKEN'),
                'client_id'    => $condominiumId,
                'start_date'   => $periodStart,
                'end_date'     => $periodEnd,
                'per_page'     => 1000,
                'page'         => $page,
            ]);

            $sales = $response->json();

            foreach ($sales as $sale) {
                $sales_value += floatval($sale['value']);
            }

            $hasMore = count($sales) === 1000;
            $page++;

        } while ($hasMore);

        $sales = Money::of($sales_value, 'BRL');

        $machineFee = $sales
            ->multipliedBy(auth()->user()->machine_fee)
            ->dividedBy(100, RoundingMode::HALF_UP);

        $taxesFee = $sales
            ->multipliedBy(auth()->user()->taxes_fee)
            ->dividedBy(100, RoundingMode::HALF_UP);

        $subtotal = $sales
            ->minus($machineFee)
            ->minus($taxesFee);

        $netValue = $subtotal->multipliedBy('0.2', RoundingMode::HALF_UP);

        \Log::info('Sales Value: ' . $sales->getAmount());
        \Log::info('Machine Fee: ' . $machineFee->getAmount());
        \Log::info('Taxes Fee: ' . $taxesFee->getAmount());
        \Log::info('Subtotal: ' . $subtotal->getAmount());
        \Log::info('Net Value: ' . $netValue->getAmount());

        $numberFormatter = new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
        $formatter = new MoneyNumberFormatter($numberFormatter);

        return [
            'sales_value' => $formatter->format($sales),
            'machine_fee' => $formatter->format($machineFee),
            'taxes_fee'   => $formatter->format($taxesFee),
            'subtotal'    => $formatter->format($subtotal),
            'net_value'   => $formatter->format($netValue),
        ];
    }
}
