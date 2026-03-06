<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Calculation;
use App\Models\Transfer;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $ids = $this->data['condominiums_ids'] ?? [];

        $apiData = collect(Cache::get('vm_clients_api', []));

        // Buscar nomes antigos do pivot para detectar mudanças
        $oldNames = DB::table('clients_condominiums')
            ->where('client_id', $this->record->id)
            ->pluck('name', 'condominium_id')
            ->toArray();

        // Montar sync com nomes da API
        $syncData = collect($ids)->mapWithKeys(function ($id) use ($apiData) {
            $name = $apiData->firstWhere('id', $id)['name'] ?? null;
            return [$id => ['name' => $name]];
        })->toArray();

        $this->record->condominiums()->sync($syncData);

        // Propagar mudanças de nome para transfers e calculations
        foreach ($syncData as $condominiumId => $pivot) {
            $oldName = $oldNames[$condominiumId] ?? null;
            $newName = $pivot['name'];

            if ($oldName && $newName && $oldName !== $newName) {
                Transfer::where('client_id', $this->record->id)
                    ->where('condominium_name', $oldName)
                    ->update(['condominium_name' => $newName]);

                Calculation::where('client_id', $this->record->id)
                    ->where('condominium_name', $oldName)
                    ->update(['condominium_name' => $newName]);
            }
        }
    }
}
