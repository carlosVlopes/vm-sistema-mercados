<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Js;
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
                ->modalHeading('Síndico criado com sucesso!')
                ->modalDescription('Copie o link abaixo e envie para o seu síndico cadastrar a senha de acesso ao painel.')
                ->modalIcon('heroicon-o-check-circle')
                ->modalIconColor('success')
                ->modalWidth('xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fechar')
                ->modalCloseButton(false)
                ->closeModalByClickingAway(false)
                ->modalCancelAction(
                    Action::make('cancel')
                        ->label('Fechar')
                        ->color('gray')
                        ->url(fn () => static::getUrl())
                )
                ->form([
                    TextInput::make('url')
                        ->label('Link de cadastro de senha')
                        ->default(fn () => $this->registerUrl)
                        ->readOnly()
                        ->helperText('Clique no ícone ao lado para copiar o link.')
                        ->suffixAction(
                            Action::make('copy')
                                ->icon('heroicon-o-clipboard-document')
                                ->color('primary')
                                ->tooltip('Copiar link')
                                ->action(function () {
                                    $this->js('navigator.clipboard.writeText(' . Js::from($this->registerUrl) . ')');

                                    Notification::make()
                                        ->title('Link copiado!')
                                        ->success()
                                        ->duration(2000)
                                        ->send();
                                })
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

        $apiData = collect(\Illuminate\Support\Facades\Cache::get('vm_clients_api', []));
        $syncData = collect($ids)->mapWithKeys(function ($id) use ($apiData) {
            $name = $apiData->firstWhere('id', $id)['name'] ?? null;
            return [$id => ['name' => $name]];
        })->toArray();

        $this->record->condominiums()->sync($syncData);

        $this->registerUrl = route('registrar-senha', [
            'token' => $this->record->register_token
        ]);

        Notification::make()
            ->title('Cliente criado com sucesso')
            ->success()
            ->send();

        $this->form->fill();

        $this->mountAction('registerLink');

        $this->halt();
    }
}
