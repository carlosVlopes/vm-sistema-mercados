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
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Icon;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Text;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Hidden;

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

                            $set('total_bruto', $sales['sales_value']);
                            $set('taxa', $sales['machine_fee']);
                            $set('impostos', $sales['taxes_fee']);
                            $set('total_liquido', $sales['subtotal']);
                            $set('comissao', $sales['net_value']);
                        })
                        ->schema([
                            Section::make('Dados do repasse')
                                ->description('Preencha todos os campos para calcular o valor do repasse automaticamente.')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Select::make('client_id')
                                                ->label('Síndico')
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
                                ])
                        ]),
                    Step::make('Resumo Financeiro')
                        ->schema([
                            Section::make('Dados do Síndico')
                                ->description('Dados do síndico selecionado')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('nome')
                                                ->label('Nome')
                                                ->disabled(),
                                            TextInput::make('email')
                                                ->label('Email')
                                                ->disabled(),
                                        ])
                                ]),
                            Section::make('Resumo Financeiro')
                                ->icon(Icon::make(Heroicon::ChartBar))
                                ->description('Visão geral dos valores')
                                ->schema([

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Cards Superiores
                                    |--------------------------------------------------------------------------
                                    */
                                    Grid::make(4)
                                        ->schema([

                                            // Total Bruto
                                            TextInput::make('total_bruto')
                                                ->label('Total Bruto')
                                                ->prefix('R$')
                                                ->disabled()
                                                ->beforeLabel(Icon::make(Heroicon::Banknotes)),
                                                // ->belowLabel('heroicon-o-banknotes'),

                                            // Taxa
                                            TextInput::make('taxa')
                                                ->label('Taxa da Máquina')
                                                ->afterLabel(Text::make(auth()->user()->machine_fee . '%')->badge())
                                                ->prefix('-')
                                                ->disabled()
                                                ->beforeLabel(Icon::make(Heroicon::CreditCard)),

                                            // Impostos
                                            TextInput::make('impostos')
                                                ->label('Impostos')
                                                ->afterLabel(Text::make(auth()->user()->taxes_fee . '%')->badge())
                                                ->prefix('-')
                                                ->disabled()
                                                ->beforeLabel(Icon::make(Heroicon::ReceiptPercent)),
                                            TextInput::make('total_liquido')
                                                ->label('Total Líquido')
                                                ->prefix('R$')
                                                ->disabled()
                                                ->default('10.530,00')
                                        ]),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Destaque Principal
                                    |--------------------------------------------------------------------------
                                    */
                                    Grid::make(1)
                                        ->schema([
                                            TextInput::make('comissao')
                                                ->label('Valor do Repasse (20%)')
                                                ->afterLabel(Text::make('Calculado sobre o total líquido.')->badge())
                                                ->prefix('R$')
                                                ->beforeLabel(Icon::make(Heroicon::ArrowTrendingUp)),
                                        ]),


                                ])
                        ]),
                ])->columnSpanFull()
                ->nextAction(fn(Action $action) => $action->label('Calcular'))
            ]);
    }
}
