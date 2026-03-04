<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\TransferResource;
use Filament\Actions\Action;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Storage;

class ViewTransfer extends ViewRecord
{
    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Group::make([
                            Section::make('Informações do Condomínio')
                                ->schema([
                                    TextEntry::make('id')
                                        ->label('Repasse')
                                        ->getStateUsing(fn ($record) => "#{$record->id}")
                                        ->weight(FontWeight::Bold)
                                        ->color('gray'),
                                    TextEntry::make('condominium_name')
                                        ->label('Condomínio')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large)
                                        ->color('primary')
                                        ->icon('heroicon-m-building-office-2'),
                                
                                    TextEntry::make('date')
                                        ->label('Registrado em')
                                        ->dateTime('d/m/Y H:i'),
                                    TextEntry::make('period_range')
                                        ->label('Período')
                                        ->getStateUsing(fn ($record) => 
                                            date('d/m/Y', strtotime($record->period_start)) . ' até ' . date('d/m/Y', strtotime($record->period_end))
                                        )
                                        ->icon('heroicon-m-calendar-days'),
                                ])->columns(1),
                        ])->columnSpan(1),
                        Group::make([
                            Section::make('Resumo Financeiro')
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            TextEntry::make('gross_total')
                                                ->label('Vendas Brutas')
                                                ->money('BRL', divideBy: 100)
                                                ->icon('heroicon-m-banknotes')
                                                ->color('gray'),
                                            TextEntry::make('percentage_value')
                                                ->label('Valor da Porcentagem')
                                                ->getStateUsing(fn ($record) => $record->light_value
                                                    ? $record->transfer_value - $record->light_value
                                                    : $record->transfer_value
                                                )
                                                ->money('BRL', divideBy: 100)
                                                ->icon('heroicon-m-receipt-percent')
                                                ->color('info'),
                                            TextEntry::make('light_value')
                                                ->label('Conta de Luz')
                                                ->money('BRL', divideBy: 100)
                                                ->icon('heroicon-m-bolt')
                                                ->visible(fn ($record) => $record->light_value)
                                                ->color('warning'),
                                        ]),
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('transfer_value')
                                                ->label('Total do Repasse')
                                                ->money('BRL', divideBy: 100)
                                                ->weight(FontWeight::Black)
                                                ->size(TextSize::Large)
                                                ->icon('heroicon-m-currency-dollar')
                                                ->color('success')
                                                ->extraAttributes(['class' => 'text-center']),
                                        ])
                                        ->extraAttributes(['class' => 'bg-success-950/10 ring-success-500/20']),
                                ]),
                                Section::make('Observações')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('notes')
                                                    ->hiddenLabel()
                                                    ->placeholder('Sem observações cadastradas.')
                                                    ->columnSpan(2),
                                            ]),
                                    ])->columnSpanFull(),
                                Section::make('Comprovantes')
                                        ->schema([
                                            Actions::make([
                                                Action::make('view_payment')
                                                    ->label('Ver Comprovante')
                                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                                    ->color('primary')
                                                    ->url(fn ($record) => $record->proof_payment ? Storage::disk('public')->url($record->proof_payment) : '#')
                                                    ->openUrlInNewTab()
                                                    ->disabled(fn ($record) => !$record->proof_payment),

                                                Action::make('view_light')
                                                    ->label('Ver Conta de Luz')
                                                    ->icon('heroicon-m-bolt')
                                                    ->color('warning')
                                                    ->url(fn ($record) => $record->proof_light ? Storage::disk('public')->url($record->proof_light) : '#')
                                                    ->openUrlInNewTab()
                                                    ->visible(fn ($record) => $record->proof_light),
                                            ]),
                                        ])->columnSpanFull(),
                            ])->columnSpan(2),
                    ])->columnSpanFull()
            ]);
    }
}
