<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Brick\Money\Money;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Illuminate\Support\HtmlString;

class EditTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Síndico')
                    ->icon(Icon::make(Heroicon::BuildingStorefront))
                    ->description('Dados de referência do repasse (somente leitura)')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('disabled_client_name')
                                    ->label('Nome')
                                    ->formatStateUsing(fn ($record) => $record->client->name)
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('disabled_email')
                                    ->label('Email')
                                    ->formatStateUsing(fn ($record) => $record->client->email)
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('disabled_condominium')
                                    ->label('Condomínio')
                                    ->formatStateUsing(fn ($record) => $record->condominium_name)
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('disabled_percentage')
                                    ->label('Porcentagem')
                                    ->prefix('%')
                                    ->formatStateUsing(fn ($record) => $record->client->percentage)
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),
                Section::make('Resumo Financeiro')
                    ->icon(Icon::make(Heroicon::Banknotes))
                    ->description('Edite os valores do repasse')
                    ->schema([
                        Placeholder::make('period')
                            ->label('Período')
                            ->content(fn ($record) => date('d/m/Y', strtotime($record->period_start)) . ' - ' . date('d/m/Y', strtotime($record->period_end))),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('disabled_gross_total')
                                    ->label('Total Bruto')
                                    ->prefix('R$')
                                    ->formatStateUsing(fn ($record) => number_format($record->gross_total / 100, 2, ',', '.'))
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('disabled_net_total')
                                    ->label('Total Líquido')
                                    ->prefix('R$')
                                    ->formatStateUsing(fn ($record) => number_format($record->net_total / 100, 2, ',', '.'))
                                    ->disabled()
                                    ->dehydrated(false),
                                DatePicker::make('date')
                                    ->label('Data do Repasse')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->required(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('transfer_value')
                                    ->label('Valor do Repasse')
                                    ->prefix('R$')
                                    ->formatStateUsing(function ($state, $record) {
                                        if (! $state) return '0,00';
                                        $value = $state;
                                        if ($record && $record->light_value) {
                                            $value = $state - $record->light_value;
                                        }
                                        return number_format($value / 100, 2, ',', '.');
                                    })
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
                                    ->required()
                                    ->beforeLabel(Icon::make(Heroicon::ArrowTrendingUp)),
                                TextInput::make('light_value')
                                    ->label('Valor da Conta de Luz')
                                    ->prefix('R$')
                                    ->formatStateUsing(function ($state) {
                                        if (! $state) return '0,00';
                                        return number_format($state / 100, 2, ',', '.');
                                    })
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
                                    ->beforeLabel(Icon::make(Heroicon::Bolt))
                                    ->visible(fn ($record) => $record?->client?->receives_light),
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
                            }),
                    ]),
                Section::make('Observações')
                    ->icon(Icon::make(Heroicon::InformationCircle))
                    ->description('Caso queira colocar alguma observação para seu síndico ver.')
                    ->schema([
                        Textarea::make('notes')
                            ->hiddenLabel()
                            ->placeholder('Sem observações cadastradas.'),
                    ]),
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
                    ]),
            ]);
    }
}
