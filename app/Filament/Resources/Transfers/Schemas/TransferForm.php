<?php

namespace App\Filament\Resources\Transfers\Schemas;

use App\Filament\Resources\Transfers\TransferResource;
use App\Models\Calculation;
use App\Models\Client;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public static function getCalcularStep(): array
    {
        return [
            Grid::make(2)
                ->schema([
                    Hidden::make('calc_id'),
                    Hidden::make('user_id')
                        ->default(fn () => auth()->id()),
                    Select::make('client_id')
                        ->label('Síndico')
                        ->relationship('client', 'name', fn ($query) => $query->where('user_id', auth()->id()))
                        ->searchable(['name', 'email'])
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('condominium_id', null))
                        ->required(),

                    Select::make('condominium_id')
                        ->label('Condomínio')
                        ->options(function ($state, callable $get) {

                            $clientId = $get('client_id');

                            if (! $clientId) {
                                return [];
                            }

                            return TransferResource::get_client_condominiums($clientId);
                        })
                        ->disabled(fn (callable $get) => ! $get('client_id'))
                        ->placeholder('Selecione um cliente primeiro')
                        ->searchable()
                        ->reactive()
                        ->required(),
                ]),
            Grid::make(2)
                ->schema([
                    Hidden::make('finished')->default(false),
                    DatePicker::make('period_start')
                        ->native(false)
                        ->label('Início das vendas')
                        ->displayFormat('d/m/Y')
                        ->required(),

                    DatePicker::make('period_end')
                        ->native(false)
                        ->label('Fim das vendas')
                        ->displayFormat('d/m/Y')
                        ->after('period_start')
                        ->required(),
                ]),
        ];
    }

    public static function getDetailsStep() : array
    {
        return [
            Placeholder::make('progress')
                ->hiddenLabel()
                ->content(function ($get) {
                    $calc = Calculation::find($get('calc_id'));

                    $isError = $calc?->status === 'error';
                    $progress = $calc?->progress ?? 0;
                    $processedDays = $calc?->processed_days ?? 0;
                    $totalDays = $calc?->total_days ?? 0;
                    $statusText = match(true) {
                        !$calc => 'Iniciando sincronização...',
                        $progress < 25 => 'Buscando vendas na API...',
                        $progress < 75 => 'Processando vendas...',
                        default => 'Finalizando cálculos...',
                    };

                    return new HtmlString(
                        Blade::render(
                            file_get_contents(resource_path('views/filament/transfer-progress.blade.php')),
                            [
                                'isError' => $isError,
                                'progress' => $progress,
                                'processedDays' => $processedDays,
                                'totalDays' => $totalDays,
                                'statusText' => $statusText,
                            ]
                        )
                    );
                })->visible(fn ($get) => filled($get('calc_id')) && Calculation::find($get('calc_id'))?->status !== 'done'),
            Hidden::make('condominium_name'),
            Group::make([
                Section::make('Dados do Síndico')
                    ->icon(Icon::make(Heroicon::BuildingStorefront))
                    ->description('Dados do síndico selecionado')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('disabled_client_name')
                                    ->label('Nome')
                                    ->disabled(),
                                TextInput::make('disabled_email')
                                    ->label('Email')
                                    ->disabled(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('disabled_period')
                                    ->label('Período')
                                    ->disabled(),
                                TextInput::make('disabled_condominium')
                                    ->label('Condomínio')
                                    ->disabled(),
                                TextInput::make('disabled_percentage')
                                    ->label('Porcentagem')
                                    ->prefix('%')
                                    ->disabled(),
                            ])
                    ])->visible(fn ($get) => optional(Calculation::find($get('calc_id')))->status === 'done'),
                Section::make('Resumo Financeiro')
                    ->icon(Icon::make(Heroicon::Banknotes))
                    ->description('Visão geral dos valores')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Hidden::make('gross_total'),
                                TextInput::make('gross_total_disabled')
                                    ->label('Total Bruto')
                                    ->prefix('R$')
                                    ->disabled(),
                                TextInput::make('machine_fee')
                                    ->label('Taxa da Máquina')
                                    ->afterLabel(Text::make(auth()->user()->machine_fee . '%')->badge()->color('info'))
                                    ->prefix('-')
                                    ->disabled(),
                                TextInput::make('taxes_fee')
                                    ->label('Impostos')
                                    ->afterLabel(Text::make(auth()->user()->taxes_fee . '%')->badge()->color('info'))
                                    ->prefix('-')
                                    ->disabled(),
                                TextInput::make('net_total')
                                    ->label('Total Líquido')
                                    ->prefix('R$')
                                    ->disabled()
                                    ->dehydrated()
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('transfer_value')
                                    ->label(function (callable $get) {
                                        $percentage = $get('disabled_percentage');

                                        return $percentage
                                            ? "Valor do Repasse ({$percentage}%)"
                                            : "Valor do Repasse";
                                    })
                                    ->afterLabel(Text::make('Calculado sobre o total líquido.')->badge()->color('info'))
                                    ->prefix('R$')
                                    ->mask(RawJs::make(<<<'JS'
                                        function(input) {
                                            let digits = input.replace(/\D/g, '');
                                            if (digits.length === 0) return ',';
                                            while (digits.length < 3) digits = '0' + digits;
                                            let intPart = digits.slice(0, -2);
                                            let mask = '';
                                            let count = 0;
                                            for (let i = intPart.length - 1; i >= 0; i--) {
                                                if (count > 0 && count % 3 === 0) mask = '.' + mask;
                                                mask = '9' + mask;
                                                count++;
                                            }
                                            return mask + ',99';
                                        }
                                    JS))
                                    ->stripCharacters('.')
                                    ->live()
                                    ->beforeLabel(Icon::make(Heroicon::ArrowTrendingUp)),
                                TextInput::make('light_value')
                                    ->label('Valor da Conta de Luz')
                                    ->prefix('R$')
                                    ->mask(RawJs::make(<<<'JS'
                                        function(input) {
                                            let digits = input.replace(/\D/g, '');
                                            if (digits.length === 0) return ',';
                                            while (digits.length < 3) digits = '0' + digits;
                                            let intPart = digits.slice(0, -2);
                                            let mask = '';
                                            let count = 0;
                                            for (let i = intPart.length - 1; i >= 0; i--) {
                                                if (count > 0 && count % 3 === 0) mask = '.' + mask;
                                                mask = '9' + mask;
                                                count++;
                                            }
                                            return mask + ',99';
                                        }
                                    JS))
                                    ->stripCharacters('.')
                                    ->live()
                                    ->default(null)
                                    ->beforeLabel(Icon::make(Heroicon::Bolt))
                                    ->visible(function (callable $get) {
                                        $clientId = $get('client_id');
                                        if (!$clientId) return false;
                                        return (bool) Client::where('id', $clientId)->value('receives_light');
                                    }),
                            ]),
                        Placeholder::make('total_repasse')
                            ->hiddenLabel()
                            ->content(function (callable $get) {
                                $parseBrl = fn ($value) => (float) str_replace(',', '.', str_replace('.', '', $value ?? '0'));

                                $transfer = $parseBrl($get('transfer_value'));
                                $light = $parseBrl($get('light_value'));
                                $total = $transfer + $light;

                                $formatted = 'R$ ' . number_format($total, 2, ',', '.');

                                $label = $light > 0
                                    ? 'Total do Repasse (Repasse + Luz)'
                                    : 'Total do Repasse';

                                return new HtmlString('
                                    <div style="text-align: center; padding: 12px 0;">
                                        <span style="color: #6b7280; font-size: 0.875rem;">' . $label . '</span>
                                        <div style="color: #16a34a; font-size: 1.875rem; font-weight: 700; line-height: 1.2;">' . $formatted . '</div>
                                    </div>
                                ');
                            })
                    ])->visible(fn ($get) => optional(Calculation::find($get('calc_id')))->status === 'done'),
                Section::make('Observações')
                    ->icon(Icon::make(Heroicon::InformationCircle))
                    ->description('Caso queria colocar alguma observação para seu síndico ver.')
                    ->schema([
                        Textarea::make('notes')
                            ->hiddenLabel()
                            ->placeholder('Sem observações cadastradas.'),
                    ])->visible(fn ($get) => optional(Calculation::find($get('calc_id')))->status === 'done'),
                Section::make('Comprovantes')
                    ->icon(Icon::make(Heroicon::Photo))
                    ->description('Anexe até 5 comprovantes relacionados a este repasse.')
                    ->schema([
                        FileUpload::make('proof_files')
                            ->label('Comprovantes')
                            ->multiple()
                            ->maxFiles(5)
                            ->reorderable()
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->disk('public')
                            ->visibility('public'),
                    ])->visible(fn ($get) => optional(Calculation::find($get('calc_id')))->status === 'done')
            ])
            ->reactive()
            ->poll(function ($get, $set) {
                if (!filled($get('calc_id'))) return null;

                if ($get('finished')) return null;

                $calc = Calculation::find($get('calc_id'));

                if (!$calc) return '2s';

                if ($calc->status === 'error') return null;

                if ($calc->status !== 'done') return '2s';

                TransferResource::setInfos($calc, $set);
                $set('finished', true);

                return null;
            })->visible(fn ($get) => filled($get('calc_id'))),
        ];
    }
}
