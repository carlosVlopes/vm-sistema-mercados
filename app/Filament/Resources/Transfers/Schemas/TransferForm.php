<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use App\Filament\Resources\Transfers\TransferResource;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Calcular')
                        ->afterValidation(function($get, $set) {
                            $sales = TransferResource::fetch_sales($get);
                        })
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    Select::make('client_id')
                                        ->label('Cliente')
                                        ->relationship('client', 'name')
                                        ->searchable(['name', 'email'])
                                        ->live()
                                        ->afterStateUpdated(fn ($set) => $set('condominium_id', null))
                                        ->required(),

                                    Select::make('condominium_id')
                                        ->label('Condomínio')
                                        ->options(function (callable $get) {

                                            $clientId = $get('client_id');

                                            if (! $clientId) {
                                                return [];
                                            }

                                            return cache()->remember(
                                                "client_{$clientId}_condominiums",
                                                600,
                                                fn () => TransferResource::get_client_condominiums($clientId)
                                            );
                                        })
                                        ->disabled(fn (callable $get) => ! $get('client_id'))
                                        ->placeholder('Selecione um cliente primeiro')
                                        ->searchable()
                                        ->required(),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    DatePicker::make('period_start')
                                        ->native(false)
                                        ->label('Início das vendas')
                                        ->displayFormat('d/m/Y')
                                        ->required(),

                                    DatePicker::make('period_end')
                                        ->native(false)
                                        ->label('Fim das vendas')
                                        ->displayFormat('   d/m/Y')
                                        ->after('period_start')
                                        ->required(),
                                    
                                ]),
                        ]),
                    Step::make('Vendas')
                        ->schema([
                            
                        ]),
                ])->columnSpanFull()
            ]);
    }
}
