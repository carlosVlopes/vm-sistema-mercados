<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Filament\Resources\Clients\ClientResource;
use App\Models\Client;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
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
                                    ->multiple()
                                    ->searchable()
                                    ->required()
                                    ->formatStateUsing(function ($record) {
                                        return \DB::table('clients_condominiums')
                                            ->where('client_id', $record?->id)
                                            ->pluck('condominium_id')
                                            ->toArray();
                                    })
                                    ->dehydrated(false),
                                TextInput::make('phonenumber')
                                    ->label('Telefone')
                                    ->tel()
                                    ->mask('(99) 99999-9999')
                                    ->maxLength(20),

                                TextInput::make('email')
                                    ->label('E-mail')
                                    ->email()
                                    ->required()
                                    ->unique(table: Client::class, column: 'email', ignoreRecord: true)
                                    ->validationMessages([
                                        'unique' => 'Este e-mail já está em uso por outro síndico.'
                                    ])
                                    ->maxLength(255),
                                TextInput::make('percentage')
                                    ->label('Percentual (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->required()
                                    ->default(0)
                                    ->step(0.01),
                                Toggle::make('receives_light')
                                    ->label('Cliente recebe valor da conta de luz?')
                                    ->inline(false)
                                    ->default(false),
                            ]), 
                    ])->columnSpanFull()
            ]);
    }
    
}
