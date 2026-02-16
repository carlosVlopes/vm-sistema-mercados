<?php

namespace App\Filament\Resources\Transfers\Tables;

use App\Filament\Resources\Transfers\TransferResource;
use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                    ->width('120px')
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
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
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
                    ->formatStateUsing(function($state){
                        return Money::ofMinor($state, 'BRL')->formatTo('pt_BR');
                    })
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('Ver Comprovante')
                        ->icon('heroicon-o-eye')
                        ->url('teste'),
                    Action::make('Ver Comprovante luz')
                        ->icon('heroicon-o-eye')
                        ->url('teste'),
                    EditAction::make(),
                    DeleteAction::make()
                        ->requiresConfirmation()
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
