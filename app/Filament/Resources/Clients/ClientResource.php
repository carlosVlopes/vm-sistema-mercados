<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClient;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'cliente';

    protected static ?string $modelLabel = 'cliente'; // texto do botao/inserir/edita

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClient::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function get_condominiums(?int $clientId = null): array
    {
        $apiData = Cache::remember('vm_clients_api', 600, function () {

            $response = Http::get('https://vmpay.vertitecnologia.com.br/api/v1/clients', [
                'access_token' => env('VM_API_TOKEN'),
            ]);

            return $response->json();
        });

        $query = \DB::table('clients_condominiums');

        if ($clientId) {
            $query->where('client_id', '!=', $clientId);
        }

        $usedIds = $query
            ->pluck('condominium_id')
            ->toArray();

        return collect($apiData)
            ->reject(fn ($item) => in_array($item['id'], $usedIds))
            ->pluck('name', 'id')
            ->toArray();
    }



}
