<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Nome
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                Select::make('condominiums_ids')
                    ->label('Condomínios')
                    ->options(function ($livewire) {
                        $clientId = $livewire->record?->id;

                        return ClientResource::get_condominiums($clientId);
                    })
                    ->loadStateFromRelationshipsUsing(function ($record, $set) {

                        $ids = \DB::table('clients_condominiums')
                            ->where('client_id', $record->id)
                            ->pluck('condominium_id')
                            ->toArray();

                        $set('condominiums_ids', $ids);
                        $set('condominiums_ids', $ids);
                    })
                    ->multiple()
                    ->searchable(),
                // Telefone
                TextInput::make('phonenumber')
                    ->label('Telefone')
                    ->tel()
                    ->mask('(99) 99999-9999')
                    ->maxLength(20),

                // Email
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->unique(table: Client::class, column: 'email', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Este e-mail já está em uso por outro cliente.'
                    ])
                    ->maxLength(255),
                
                // Percentual
                TextInput::make('percentage')
                    ->label('Percentual (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required()
                    ->step(0.01),

                // Recebe luz
                Toggle::make('receives_light')
                    ->label('Cliente recebe valor da conta de luz?')
                    ->inline(false)
                    ->default(false),
            ]);
    }

    
}
