<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;

    public ?string $registerUrl = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('registerLink')
                ->label('Link de cadastro de senha')
                ->modalHeading('Envie esse link para seu cliente cadastrar a senha.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar')
                ->form([
                    TextInput::make('url')
                        ->label('URL')
                        ->default(fn () => $this->registerUrl)
                        ->readOnly()
                        ->suffixAction(
                            Action::make('copy')
                                ->icon('heroicon-o-clipboard')
                                ->action(fn ($state) => null)
                                ->extraAttributes([
                                    'x-on:click' => 'navigator.clipboard.writeText($el.closest("[x-data]").querySelector("input").value)'
                                ])
                        ),
                ])
                ->visible(fn () => filled($this->registerUrl)),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        $data['register_token'] = hash_hmac('sha256', Str::random(60), $data['name'] . $data['email']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $ids = $this->data['condominiums_ids'] ?? [];

        $this->record->condominiums()->sync($ids);

        $this->registerUrl = route('registrar-senha', [
            'token' => $this->record->register_token
        ]);

        Notification::make()
            ->title('Cliente criado com sucesso')
            ->body('Copie o link para o cliente registrar a senha.')
            ->success()
            ->send();

        $this->form->fill();

        $this->mountAction('registerLink');

        $this->halt();
    }
}
