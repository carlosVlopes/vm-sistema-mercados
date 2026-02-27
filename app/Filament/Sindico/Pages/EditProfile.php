<?php

namespace App\Filament\Sindico\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações Pessoais')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('phonenumber')
                            ->label('Telefone')
                            ->tel()
                            ->maxLength(255),
                    ]),
                Section::make('Alterar Senha')
                    ->schema([
                        TextInput::make('password')
                            ->label('Nova senha')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar nova senha')
                            ->password()
                            ->revealable()
                            ->dehydrated(false),
                        TextInput::make('current_password')
                            ->label('Senha atual')
                            ->password()
                            ->revealable()
                            ->requiredWith('password')
                            ->currentPassword()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
