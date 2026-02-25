<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\Schemas\TransferForm;
use App\Filament\Resources\Transfers\TransferResource;
use Brick\Money\Money;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;

    use CreateRecord\Concerns\HasWizard;

    protected function getSteps(): array
    {
        return [
            Step::make('Informações Básicas')
                ->afterValidation(function($get, $set) {
                    $calc = TransferResource::fetch_sales($get);

                    $set('calc_id', $calc->id);     
                    $set('calc_status', $calc->status);

                    if ($calc?->status !== 'done') {
                        Notification::make()
                            ->title('Processando vendas...')
                            ->warning()
                            ->send();
                    }
                })
                ->schema([
                    Section::make()
                        ->schema(TransferForm::getCalcularStep())
                ]),
            Step::make('Resumo Financeiro')
                ->schema([
                    Section::make()
                        ->schema(TransferForm::getDetailsStep()),
                ]),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['condominium_id']);

        $normalized_transfer_value = str_replace('.', '', $data['transfer_value']);
        $normalized_transfer_value = str_replace(',', '.', $normalized_transfer_value);

        $normalized_gross_total = str_replace('.', '', $data['gross_total']);
        $normalized_gross_total = str_replace(',', '.', $normalized_gross_total);

        $normalized_light_value = str_replace('.', '', $data['light_value']);
        $normalized_light_value = str_replace(',', '.', $normalized_light_value);

        $data['transfer_value'] = Money::of($normalized_transfer_value, 'BRL')->getMinorAmount()->toInt();
        $data['gross_total'] = Money::of($normalized_gross_total, 'BRL')->getMinorAmount()->toInt();
        $data['light_value'] = Money::of($normalized_light_value, 'BRL')->getMinorAmount()->toInt();

        return $data;
    }

}
