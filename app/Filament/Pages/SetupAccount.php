<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;

class SetupAccount extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.setup-account';

    protected static ?string $title = '';

    protected static bool $shouldRegisterNavigation = false;

    // 1. Propriedade para segurar os dados do form
    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        $this->data = [
            'machine_fee' => $user->machine_fee,
            'taxes_fee' => $user->taxes_fee,
            'api_token' => $user->api_token,
        ];
    }

    // 3. Método Form (Não estático e recebe Form $form)
    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([ 
                Section::make('Configurações de Taxas e API')
                    ->description('Configure a taxa de máquina, impostos e o token da API para começar a usar o sistema.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('machine_fee')
                                    ->label('Taxa de Máquina (%)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%'),
                                
                                TextInput::make('taxes_fee')
                                    ->label('Taxa de Impostos (%)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%'),
                            ]),
                        TextInput::make('api_token')
                            ->label('Token da API')
                            ->required()
                    ])
            ]);
    }

    // 4. Método para salvar
    public function save(): void
    {
        $data = $this->form->getState();

        $response = Http::get('https://vmpay.vertitecnologia.com.br/api/v1/clients', [
            'access_token' => $data['api_token'],
        ]);

        if($response->status() != 200)
        {
            Notification::make()
                ->danger()
                ->title('Não conseguimos validar esse token, por favor verifique o token e tente novamente!')
                ->send();

            return;
        }

        $user = auth()->user();

        $user->machine_fee = $data['machine_fee'];
        $user->taxes_fee = $data['taxes_fee'];
        $user->api_token = $data['api_token'];
        $user->save();

        Notification::make()
            ->success()
            ->title('Configurações salvas com sucesso!')
            ->send();

        $this->redirect(route('filament.painel.pages.dashboard'));
    }
}