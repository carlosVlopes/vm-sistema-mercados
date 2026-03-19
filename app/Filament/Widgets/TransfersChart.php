<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Transfer;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Facades\DB;

class TransfersChart extends ChartWidget
{
    use HasFiltersSchema;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Repasses';

    protected ?string $maxHeight = '400px';

    public function filtersSchema(Schema $schema): Schema
    {
        $condominiums = Transfer::query()
            ->where('user_id', auth()->id())
            ->whereNotNull('condominium_name')
            ->distinct()
            ->orderBy('condominium_name')
            ->pluck('condominium_name', 'condominium_name')
            ->toArray();

        $clients = Client::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        return $schema->components([
            Select::make('condominium_name')
                ->label('Condomínio')
                ->options(['' => 'Todos'] + $condominiums)
                ->default(''),

            Select::make('client_id')
                ->label('Cliente')
                ->options(['' => 'Todos'] + $clients)
                ->default(''),
        ]);
    }

    protected function getData(): array
    {
        $months = collect(range(1, 12));

        $query = Transfer::query()
            ->where('user_id', auth()->id())
            ->whereBetween('period_start', [now()->startOfYear(), now()->endOfYear()]);

        if (! empty($this->filters['condominium_name'])) {
            $query->where('condominium_name', $this->filters['condominium_name']);
        }

        if (! empty($this->filters['client_id'])) {
            $query->where('client_id', $this->filters['client_id']);
        }

        $results = $query
            ->select(DB::raw('MONTH(period_start) as month, SUM(transfer_value) as total'))
            ->groupBy(DB::raw('MONTH(period_start)'))
            ->get()
            ->keyBy('month');

        $labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        $data = $months->map(fn ($m) => round(($results->get($m)?->total ?? 0) / 100, 2))->values();

        return [
            'datasets' => [
                [
                    'label' => 'Repasses',
                    'data' => $data,
                    'backgroundColor' => '#3fec79',
                    'borderColor' => '#b1ffc4',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y ?? context.parsed;
                                return ' R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
