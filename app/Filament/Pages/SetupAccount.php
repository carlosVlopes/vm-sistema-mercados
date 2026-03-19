<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Http;

class SetupAccount extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.setup-account';

    protected static ?string $title = '';

    protected static ?string $slug = 'configuracoes';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public ?string $status = ''; // null, 'loading', 'error', 'success'

    public bool $isFirstSetup = false;

    public function getLayout(): string
    {
        if ($this->isFirstSetup) {
            return 'filament-panels::components.layout.simple';
        }

        return parent::getLayout();
    }

    public function getMaxContentWidth(): Width | string | null
    {
        if ($this->isFirstSetup) {
            return Width::FourExtraLarge;
        }

        return parent::getMaxContentWidth();
    }

    protected function getLayoutData(): array
    {
        if ($this->isFirstSetup) {
            return [
                'hasTopbar' => false,
                'maxContentWidth' => $this->getMaxContentWidth(),
                'maxWidth' => $this->getMaxContentWidth(),
            ];
        }

        return parent::getLayoutData();
    }

    public function mount(): void
    {
        $user = auth()->user();

        $this->isFirstSetup = ! $user->isConfigured();

        $this->data = [
            'machine_fee' => $user->machine_fee,
            'taxes_fee' => $user->taxes_fee,
            'api_token' => $user->api_token,
        ];
    }

    public function form(Schema $schema): Schema
    {
        $fields = [
            Grid::make(2)
                ->schema([
                    TextInput::make('machine_fee')
                        ->label('Taxa de Máquina (%)')
                        ->numeric()
                        ->required()
                        ->default('0')
                        ->suffix('%'),

                    TextInput::make('taxes_fee')
                        ->label('Taxa de Impostos (%)')
                        ->numeric()
                        ->required()
                        ->default('0')
                        ->suffix('%'),
                ]),
            TextInput::make('api_token')
                ->label('Token da API')
                ->required(),
        ];

        if ($this->isFirstSetup) {
            return $schema
                ->statePath('data')
                ->components($fields);
        }

        return $schema
            ->statePath('data')
            ->components($fields);
    }

    public function save(): void
    {
        $this->form->getState();

        $this->status = 'loading';

        $this->validateToken();
    }

    public function validateToken(): void
    {
        $data = $this->data;

        $response = Http::get('https://vmpay.vertitecnologia.com.br/api/v1/clients', [
            'access_token' => $data['api_token'],
        ]);

        if ($response->status() != 200) {
            $this->status = 'error';

            return;
        }

        $user = auth()->user();

        $user->machine_fee = $data['machine_fee'];
        $user->taxes_fee = $data['taxes_fee'];
        $user->api_token = $data['api_token'];
        $user->save();

        $this->status = 'success';
    }
}