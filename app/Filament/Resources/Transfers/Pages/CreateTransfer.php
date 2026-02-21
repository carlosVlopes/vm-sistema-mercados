<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\Schemas\TransferForm;
use App\Filament\Resources\Transfers\TransferResource;
use Brick\Money\Money;
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
                    $info = TransferResource::fetch_sales($get);

                    // $set('gross_total', $info['gross_total']);
                    // $set('gross_total_disabled', $info['gross_total']);
                    // $set('machine_fee', $info['machine_fee']);
                    // $set('taxes_fee', $info['taxes_fee']);
                    // $set('net_total', $info['net_total']);
                    // $set('transfer_value', $info['transfer_value']);


                    // $set('disabled_client_name', $info['client_name'] ?? '');
                    // $set('disabled_email', $info['client_email'] ?? '');
                    // $set('disabled_percentage', $info['client_percentage'] ?? 0);
                    // $set('disabled_period', date('d/m/Y', strtotime($get('period_start'))) . ' - ' . date('d/m/Y', strtotime($get('period_end'))));
                    // $set('disabled_condominium', $info['condominium_name']);
                    // $set('condominium_name', $info['condominium_name']);
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

        $data['transfer_value'] = Money::of($normalized_transfer_value, 'BRL')->getMinorAmount()->toInt();
        $data['gross_total'] = Money::of($normalized_gross_total, 'BRL')->getMinorAmount()->toInt();

        return $data;
    }

}
