<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $data['register_token'] = hash_hmac('sha256', Str::random(60), $data['name'] . $data['email']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $ids = $this->data['condominiuns_ids'] ?? [];

        $this->record->condominiums()->sync($ids);
    }
}
