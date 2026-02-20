<?php

namespace App\Filament\Resources\Transfers\Schemas;

use App\Filament\Resources\Transfers\TransferResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function getCalcularStep(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Hidden::make('user_id')
                        ->default(fn () => auth()->id()),
                    Select::make('client_id')
                        ->label('Síndico')
                        ->relationship('client', 'name')
                        ->searchable(['name', 'email'])
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('condominium_id', null))
                        ->required(),

                    Select::make('condominium_id')
                        ->label('Condomínio')
                        ->options(function ($state, callable $get) {

                            $clientId = $get('client_id');

                            if (! $clientId) {
                                return [];
                            }

                            $options = cache()->remember(
                                "client_{$clientId}_condominiums",
                                600,
                                fn () => TransferResource::get_client_condominiums($clientId)
                            );

                            return $options;
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
                        ->displayFormat('d/m/Y')
                        ->after('period_start')
                        ->required(),
                    
                ]),
        ];
    }

    public static function getDetailsStep() : array
    {
        return [
            Hidden::make('condominium_name'),
            Section::make('Dados do Síndico')
                ->icon(Icon::make(Heroicon::BuildingStorefront))
                ->description('Dados do síndico selecionado')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('disabled_client_name')
                                ->label('Nome')
                                ->disabled(),
                            TextInput::make('disabled_email')
                                ->label('Email')
                                ->disabled(),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextInput::make('disabled_period')
                                ->label('Período')
                                ->disabled(),
                            TextInput::make('disabled_condominium')
                                ->label('Condomínio')
                                ->disabled(),
                            TextInput::make('disabled_percentage')
                                ->label('Porcentagem')
                                ->prefix('%')
                                ->disabled(),
                        ])
                ]),
            Section::make('Resumo Financeiro')
                ->icon(Icon::make(Heroicon::Banknotes))
                ->description('Visão geral dos valores')
                ->schema([
                    Grid::make(4)
                        ->schema([
                            Hidden::make('gross_total'),
                            TextInput::make('gross_total_disabled')
                                ->label('Total Bruto')
                                ->prefix('R$')
                                ->disabled(),
                            TextInput::make('machine_fee')
                                ->label('Taxa da Máquina')
                                ->afterLabel(Text::make(auth()->user()->machine_fee . '%')->badge()->color('info'))
                                ->prefix('-')
                                ->disabled(),
                            TextInput::make('taxes_fee')
                                ->label('Impostos')
                                ->afterLabel(Text::make(auth()->user()->taxes_fee . '%')->badge()->color('info'))
                                ->prefix('-')
                                ->disabled(),
                            TextInput::make('net_total')
                                ->label('Total Líquido')
                                ->prefix('R$')
                                ->disabled()
                        ]),
                    Grid::make(1)
                        ->schema([
                            TextInput::make('transfer_value')
                                ->label(function (callable $get) {
                                    $percentage = $get('disabled_percentage');

                                    return $percentage
                                        ? "Valor do Repasse ({$percentage}%)"
                                        : "Valor do Repasse";
                                })
                                ->afterLabel(Text::make('Calculado sobre o total líquido.')->badge()->color('info'))
                                ->prefix('R$')
                                ->beforeLabel(Icon::make(Heroicon::ArrowTrendingUp)),
                        ]),


                ]),
            Section::make('Comprovantes')
                ->icon(Icon::make(Heroicon::Photo))
                ->description('Comprovantes relacionados a este repasse.')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            FileUpload::make('proof_payment')
                                ->label('Comprovante de transferência')
                                ->acceptedFileTypes(['application/pdf', 'application/image/jpeg', 'application/image/png'])
                                ->disk('public')
                                ->visibility('public'),
                            FileUpload::make('proof_light')
                                ->label('Comprovante de luz')
                                ->acceptedFileTypes(['application/pdf', 'application/image/jpeg', 'application/image/png'])
                                ->disk('public')
                                ->visibility('public')
                        ]),
                ])
            ];
    } 
}
