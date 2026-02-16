<?php

namespace App\Filament\Sindico\Resources\Transfers;

use App\Filament\Sindico\Resources\Transfers\Pages\ListTransfers;
use App\Filament\Sindico\Resources\Transfers\Pages\ViewTransfer;
use App\Filament\Sindico\Resources\Transfers\Tables\TransfersTable;
use App\Models\Sindico\Transfer;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
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

    public static function table(Table $table): Table
    {
        return TransfersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('client_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransfers::route('/'),
            'view' => ViewTransfer::route('/{record}'),
        ];
    }

    public static function get_condominium_name(int $condominium_id)
    {
        $apiData = Cache::remember('vm_condominium_' . $condominium_id . '_api', 600, function () use ($condominium_id) {
            $response = Http::get('https://vmpay.vertitecnologia.com.br/api/v1/clients/' . $condominium_id, [
                'access_token' => User::find(auth()->user()->user_id)->api_token,
            ]);

            return $response->json();
        });

        return $apiData['name'] ?? '';
    }
}
