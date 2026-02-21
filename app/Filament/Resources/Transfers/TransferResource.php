<?php

namespace App\Filament\Resources\Transfers;

use App\Filament\Resources\Transfers\Pages\CreateTransfer;
use App\Filament\Resources\Transfers\Pages\EditTransfer;
use App\Filament\Resources\Transfers\Pages\ListTransfers;
use App\Filament\Resources\Transfers\Schemas\TransferForm;
use App\Filament\Resources\Transfers\Tables\TransfersTable;
use App\Jobs\SyncSalesJob;
use App\Models\Calculation;
use App\Models\Transfer;
use BackedEnum;
use Brick\Money\Money;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
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
                'access_token' => auth()->user()->api_token,
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

        $client = \App\Models\Client::find($clientId);

        $period = CarbonPeriod::create(
            Carbon::parse($periodStart)->startOfDay(),
            Carbon::parse($periodEnd)->endOfDay()
        );

        $periodStartCarbon = Carbon::parse($periodStart)->startOfDay();
        $periodEndCarbon   = Carbon::parse($periodEnd)->endOfDay();

        $totalDays = $periodStartCarbon->diffInDays($periodEndCarbon) + 1;

        $calc = Calculation::create([
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'status' => 'pending',
            'progress' => 0,
            'total_days'   => $totalDays,
            'processed_days' => 0,
        ]);

        $period = CarbonPeriod::create(
            $periodStartCarbon,
            $periodEndCarbon
        );

        foreach ($period as $date) {
            SyncSalesJob::dispatch(
                calc_id: $calc->id,
                client_id: $clientId,
                condominium_id: $condominiumId,
                api_token: auth()->user()->api_token,
                user_id: auth()->id(),
                page: 1,
                day: $date->toDateString()
            );
        }

        return $calc;
    }
}
