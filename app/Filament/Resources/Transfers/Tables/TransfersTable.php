<?php

namespace App\Filament\Resources\Transfers\Tables;

use Brick\Money\Money;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Illuminate\Support\Facades\Storage;

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
                SelectFilter::make('client')
                    ->label('Cliente')
                    ->relationship('client', 'name'),
                Filter::make('period')
                    ->form([
                        DatePicker::make('period_start')
                            ->label('Início das vendas'),
                        DatePicker::make('period_end')
                            ->label('Fim das vendas'),
                    ])
                    ->columns(4) 
                    ->columnSpan(4)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['period_start'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('period_start', '>=', $date),
                            )
                            ->when(
                                $data['period_end'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('period_end', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['period_start'] ?? null) {
                            $indicators['period_start'] = 'Vendas de ' . Carbon::parse($data['period_start'])->format('d/m/Y');
                        }
                        if ($data['period_end'] ?? null) {
                            $indicators['period_end'] = 'Até' . Carbon::parse($data['period_end'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ActionGroup::make([
                    Action::make('Ver Comprovante')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => $record->proof_payment ? Storage::disk('public')->url($record->proof_payment) : '#'),
                    Action::make('Ver Comprovante luz')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => Storage::disk('public')->url($record->proof_light))
                        ->visible(fn ($record) => $record->proof_light),
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
