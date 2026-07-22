<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\Schemas\EditTransferForm;
use App\Filament\Resources\Transfers\TransferResource;
use Brick\Money\Money;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditTransfer extends EditRecord
{
    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return EditTransferForm::configure($schema);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $parseBrl = fn ($value) => str_replace(',', '.', str_replace('.', '', $value));

        $normalizedTransfer = $parseBrl($data['transfer_value']);
        $transferMoney = Money::of($normalizedTransfer, 'BRL');

        if (! empty($data['light_value'])) {
            $normalizedLight = $parseBrl($data['light_value']);
            $lightMoney = Money::of($normalizedLight, 'BRL');
            $data['light_value'] = $lightMoney->getMinorAmount()->toInt();
            $transferMoney = $transferMoney->plus($lightMoney);
        } else {
            $data['light_value'] = 0;
        }

        $data['transfer_value'] = $transferMoney->getMinorAmount()->toInt();

        return $data;
    }
}
