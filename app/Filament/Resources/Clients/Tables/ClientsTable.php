<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Nome
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                // Telefone
                TextColumn::make('phonenumber')
                    ->label('Telefone')
                    ->searchable()
                    ->sortable(),

                // Email
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),

                // Percentual
                TextColumn::make('percentage')
                    ->label('Percentual (%)')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . '%')
                    ->searchable()
                    ->sortable(),

                // Recebe luz
                ToggleColumn::make('receives_light')
                    ->label('Recebe luz?')
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('Condomínios')
                    ->button()
                    ->url('condominios?cliente='),
                EditAction::make()
                    ->button(),
                DeleteAction::make()
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
