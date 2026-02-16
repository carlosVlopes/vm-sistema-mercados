<?php

namespace App\Filament\Sindico\Resources\Transfers\Tables;

use App\Filament\Sindico\Resources\Transfers\TransferResource;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Brick\Money\Money;

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
                TextColumn::make('condominium_id')
                    ->label('Condomínio')
                    ->searchable()
                    ->formatStateUsing(function($state){
                        return TransferResource::get_condominium_name($state);
                    })
                    ->sortable(),
                TextColumn::make('transfer_value')
                    ->label('Valor do repasse')
                    ->width('200px')
                    ->formatStateUsing(function($state){
                        return Money::ofMinor($state, 'BRL')->formatTo('pt_BR');
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Visualizar')
                    ->url('teste')
                    ->button()
                    ->icon('heroicon-o-eye'),
            ])
            ->toolbarActions([]);
    }
}
