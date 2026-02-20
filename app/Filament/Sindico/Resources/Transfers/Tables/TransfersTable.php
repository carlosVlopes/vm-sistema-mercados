<?php

namespace App\Filament\Sindico\Resources\Transfers\Tables;

use Brick\Money\Money;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->button(),
            ])
            ->toolbarActions([])
            ->headerActions([]);

    }
}
