<?php

namespace App\Filament\Sindico\Widgets;

use App\Models\Transfer;
use Brick\Money\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return (auth()->user()->receives_light) ? 2 : 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Valor total de repasses', Money::ofMinor(Transfer::query()->where('client_id', auth()->id())->sum('transfer_value'), 'BRL')->formatTo('pt_BR'))
                ->color('success')
                ->icon('heroicon-m-arrow-trending-up')
                ->description(Transfer::query()->where('client_id', auth()->id())->count() . ' repasses'),
            Stat::make('Valor total com energia', Money::ofMinor(Transfer::query()->where('client_id', auth()->id())->sum('light_value'), 'BRL')->formatTo('pt_BR'))
                ->icon('heroicon-m-bolt')    
                ->color('info')
                ->visible(auth()->user()->receives_light)
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
