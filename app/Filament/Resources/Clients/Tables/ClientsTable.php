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
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;

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
                TernaryFilter::make('receives_light')
                    ->label('Recebe luz?')
                    ->nullable()
                    ->attribute('receives_light')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('receives_light'),
                        false: fn (Builder $query) => $query->whereNull('receives_light'),
                    )
            ])
            ->recordActions([
                Action::make('Repasses')
                    ->button()
                    ->url('teste'),
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
