<?php

namespace App\Filament\Sindico\Resources\Transfers\Tables;

use App\Models\Sindico\Transfer;
use Brick\Money\Money;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TransfersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Data do repasse')
                    ->date()
                    ->dateTime('d/m/Y')
                    ->width('100px')
                    ->sortable(),
                TextColumn::make('period_start')
                    ->label('Período')
                    ->date()
                    ->dateTime('d/m/Y')
                    ->formatStateUsing(function($state, $record){
                        $start = date('d/m/Y', strtotime($record->period_start));
                        $end = date('d/m/Y', strtotime($record->period_end));
                        return "{$start} - {$end}";
                    })
                    ->sortable(),
                TextColumn::make('condominium_name')
                    ->label('Condomínio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('transfer_value')
                    ->label('Valor do repasse')
                    ->weight(FontWeight::Medium)
                    ->size(TextSize::Medium)
                    ->formatStateUsing(function($state){
                        return Money::ofMinor($state, 'BRL')->formatTo('pt_BR');
                    })
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('condominium_name')
                    ->label('Condomínio')
                    ->options(
                        Transfer::query()
                            ->where('client_id', auth()->id())
                            ->distinct()
                            ->pluck('condominium_name', 'condominium_name')
                            ->toArray()
                    ),
                Filter::make('date')
                    ->form([
                        DatePicker::make('date_start')
                            ->label('Data inicial'),
                        DatePicker::make('date_end')
                            ->label('Data final'),
                    ])
                    ->columns(3) 
                    ->columnSpan(3)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_start'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_end'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_start'] ?? null) {
                            $indicators['date_start'] = 'Repasses de ' . Carbon::parse($data['date_start'])->format('d/m/Y');
                        }
                        if ($data['date_end'] ?? null) {
                            $indicators['date_end'] = 'Até ' . Carbon::parse($data['date_end'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make()
                    ->button(),
            ])
            ->toolbarActions([])
            ->headerActions([]);

    }
}
