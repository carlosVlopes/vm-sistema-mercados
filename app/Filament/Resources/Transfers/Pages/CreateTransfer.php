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

        $parseBrl = fn ($value) => str_replace(',', '.', str_replace('.', '', $value));

        $normalized_transfer_value = $parseBrl($data['transfer_value']);
        $normalized_gross_total = $parseBrl($data['gross_total']);
        $normalized_net_total = $parseBrl($data['net_total']);

        $transferMoney = Money::of($normalized_transfer_value, 'BRL');
        $data['gross_total'] = Money::of($normalized_gross_total, 'BRL')->getMinorAmount()->toInt();
        $data['net_total'] = Money::of($normalized_net_total, 'BRL')->getMinorAmount()->toInt();

        if (!empty($data['light_value'])) {
            $normalized_light_value = $parseBrl($data['light_value']);
            $lightMoney = Money::of($normalized_light_value, 'BRL');
            $data['light_value'] = $lightMoney->getMinorAmount()->toInt();
            $transferMoney = $transferMoney->plus($lightMoney);
        } else {
            $data['light_value'] = 0;
        }

        $data['transfer_value'] = $transferMoney->getMinorAmount()->toInt();

        return $data;
    }

}
