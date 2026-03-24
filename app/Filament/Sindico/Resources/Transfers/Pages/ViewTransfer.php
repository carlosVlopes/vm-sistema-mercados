<?php

namespace App\Filament\Sindico\Resources\Transfers\Pages;

use App\Filament\Sindico\Resources\Transfers\TransferResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Placeholder;

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
                                            TextEntry::make('net_total')
                                                ->label('Vendas Líquidas')
                                                ->money('BRL', divideBy: 100)
                                                ->icon('heroicon-m-banknotes')
                                                ->color('gray'),
                                            TextEntry::make('percentage_value')
                                                ->label('Repasse sobre as vendas')
                                                ->getStateUsing(fn ($record) => $record->light_value
                                                    ? $record->transfer_value - $record->light_value
                                                    : $record->transfer_value
                                                )
                                                ->money('BRL', divideBy: 100)
                                                ->icon('heroicon-m-receipt-percent')
                                                ->color('info'),
                                            TextEntry::make('light_value')
                                                ->label('Repasse energia')
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
                                        Placeholder::make('proof_files_list')
                                            ->hiddenLabel()
                                            ->content(function ($record) {
                                                $files = $record->proof_files ?? [];
                                                if (empty($files)) return 'Nenhum comprovante anexado.';

                                                $buttons = collect($files)->map(function ($file, $i) {
                                                    $url = Storage::disk('public')->url($file);
                                                    $label = 'Comprovante ' . ($i + 1);
                                                    return '<a href="' . e($url) . '" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #FC6E20; color: #fff; border-radius: 8px; font-size: 0.875rem; font-weight: 600; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,.1);">'
                                                        . '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Zm7.25-.75a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0V6.31l-5.72 5.72a.75.75 0 1 1-1.06-1.06l5.72-5.72H12.25a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>'
                                                        . e($label)
                                                        . '</a>';
                                                })->implode('');

                                                return new \Illuminate\Support\HtmlString('<div style="display: flex; flex-wrap: wrap; gap: 12px;">' . $buttons . '</div>');
                                            }),
                                    ])->columnSpanFull()->visible(fn ($record) => !empty($record->proof_files)),
                            ])->columnSpan(2),
                    ])->columnSpanFull()
            ]);
    }
}
