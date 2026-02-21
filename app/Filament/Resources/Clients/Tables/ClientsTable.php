<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;


class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#'. $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phonenumber')
                    ->label('Telefone')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('percentage')
                    ->label('Percentual (%)')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . '%')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('receives_light')
                    ->label('Recebe luz?')
            ])
            ->filters([
                TernaryFilter::make('receives_light')
                    ->label('Recebe luz?')
            ])
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->color('gray'),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->button()
                    ->requiresConfirmation()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
