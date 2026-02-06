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

        $this->condominiuns_ids = $data['condominiuns_ids'] ?? [];

        unset($data['condominiuns_ids']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! empty($this->data['condominiuns_ids'])) {

        $this->record
            ->condominiums()
            ->sync($this->data['condominiuns_ids']);
    }

        // $token = $this->record->register_token;

        // $link = url('/register/' . $token);

        // Notification::make()
        //     ->title('Link de cadastro gerado')
        //     ->body("Copie o link abaixo para enviar ao cliente:\n\n$link")
        //     ->success()
        //     ->persistent() // não some sozinho
        //     ->actions([
        //         Action::make('Copiar')
        //             ->button()
        //             ->extraAttributes([
        //                 'onclick' => "navigator.clipboard.writeText('$link')"
        //             ]),
        //     ])
        //     ->send();
    }
}
