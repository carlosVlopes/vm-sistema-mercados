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
                                    Grid::make(2)
                                        ->schema([
                                            TextEntry::make('transfer_value')
                                                ->label('Valor do Repasse')
                                                ->money('BRL', divideBy: 100)
                                                ->weight(FontWeight::Black)
                                                ->size(TextSize::Large)
                                                ->icon('heroicon-m-currency-dollar')
                                                ->color('success'),
                                            TextEntry::make('light_value')
                                                ->label('Valor conta de luz')
                                                ->money('BRL', divideBy: 100)
                                                ->weight(FontWeight::Black)
                                                ->size(TextSize::Large)
                                                ->icon('heroicon-m-bolt')
                                                ->visible(fn ($record) => $record->light_value)
                                                ->color('warning'),
                                        ]),
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
                                    ])
                                    ->extraAttributes(['style' => 'margin-top: 3.2rem !important; display: flex;'])
                                    ->label('Comprovantes')
                                    ->columnSpan(1),
                                ]),
                            ])->columnSpan(2),
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
                    ])->columnSpanFull()
            ]);
    }
}
