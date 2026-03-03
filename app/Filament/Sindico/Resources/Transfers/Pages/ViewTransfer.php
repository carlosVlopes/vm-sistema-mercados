<?php

namespace App\Filament\Sindico\Resources\Transfers\Pages;

use App\Filament\Sindico\Resources\Transfers\TransferResource;
use Filament\Actions\Action;
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
                // Top banner: condominium info + date/period
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Group::make([
                                    TextEntry::make('condominium_name')
                                        ->label('Condomínio')
                                        ->weight(FontWeight::Bold)
                                        ->size(TextSize::Large)
                                        ->color('primary')
                                        ->icon('heroicon-m-building-office-2'),
                                ])->columnSpan(1),

                                Group::make([
                                    TextEntry::make('date')
                                        ->label('Registrado em')
                                        ->dateTime('d/m/Y H:i')
                                        ->icon('heroicon-m-clock'),
                                ])->columnSpan(1),

                                Group::make([
                                    TextEntry::make('period_range')
                                        ->label('Período')
                                        ->getStateUsing(fn ($record) =>
                                            date('d/m/Y', strtotime($record->period_start)) . ' até ' . date('d/m/Y', strtotime($record->period_end))
                                        )
                                        ->icon('heroicon-m-calendar-days')
                                        ->weight(FontWeight::Medium),
                                ])->columnSpan(1),
                            ]),
                    ])
                    ->extraAttributes(['class' => 'bg-gray-950/5 dark:bg-white/5']),

                // Financial summary
                Section::make('Resumo Financeiro')
                    ->icon('heroicon-m-chart-bar')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Group::make([
                                    TextEntry::make('gross_total')
                                        ->label('Vendas Brutas')
                                        ->money('BRL', divideBy: 100)
                                        ->icon('heroicon-m-banknotes')
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::SemiBold)
                                        ->color('gray'),
                                ])->columnSpan(1),

                                Group::make([
                                    TextEntry::make('percentage_value')
                                        ->label('Valor da Porcentagem')
                                        ->getStateUsing(fn ($record) => $record->light_value
                                            ? $record->transfer_value - $record->light_value
                                            : $record->transfer_value
                                        )
                                        ->money('BRL', divideBy: 100)
                                        ->icon('heroicon-m-receipt-percent')
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::SemiBold)
                                        ->color('info'),
                                ])->columnSpan(1),

                                Group::make([
                                    TextEntry::make('light_value')
                                        ->label('Conta de Luz')
                                        ->money('BRL', divideBy: 100)
                                        ->icon('heroicon-m-bolt')
                                        ->size(TextSize::Large)
                                        ->weight(FontWeight::SemiBold)
                                        ->visible(fn ($record) => $record->light_value)
                                        ->color('warning'),
                                ])->columnSpan(1),

                                // Total highlight
                                Group::make([
                                    TextEntry::make('transfer_value')
                                        ->label('Total do Repasse')
                                        ->money('BRL', divideBy: 100)
                                        ->weight(FontWeight::Black)
                                        ->size(TextSize::ExtraLarge)
                                        ->icon('heroicon-m-currency-dollar')
                                        ->color('success'),
                                ])
                                    ->columnSpan(1)
                                    ->extraAttributes(['class' => 'rounded-xl bg-success-50 dark:bg-success-950/20 ring-1 ring-success-200 dark:ring-success-500/20 p-4']),
                            ]),
                    ]),

                // Bottom row: comprovantes + observações side by side
                Grid::make(2)
                    ->schema([
                        Section::make('Comprovantes')
                            ->icon('heroicon-m-document-check')
                            ->schema([
                                Actions::make([
                                    Action::make('view_payment')
                                        ->label('Ver Comprovante de Pagamento')
                                        ->icon('heroicon-m-arrow-top-right-on-square')
                                        ->color('primary')
                                        ->url(fn ($record) => $record->proof_payment ? Storage::disk('public')->url($record->proof_payment) : '#')
                                        ->openUrlInNewTab()
                                        ->disabled(fn ($record) => ! $record->proof_payment),

                                    Action::make('view_light')
                                        ->label('Ver Conta de Luz')
                                        ->icon('heroicon-m-bolt')
                                        ->color('warning')
                                        ->url(fn ($record) => $record->proof_light ? Storage::disk('public')->url($record->proof_light) : '#')
                                        ->openUrlInNewTab()
                                        ->visible(fn ($record) => $record->proof_light),
                                ]),
                            ]),

                        Section::make('Observações')
                            ->icon('heroicon-m-chat-bubble-bottom-center-text')
                            ->schema([
                                TextEntry::make('notes')
                                    ->hiddenLabel()
                                    ->placeholder('Sem observações cadastradas.')
                                    ->prose(),
                            ]),
                    ]),
            ]);
    }
}
