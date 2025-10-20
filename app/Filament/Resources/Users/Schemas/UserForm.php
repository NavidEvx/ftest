<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('family_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn ($record) => ! $record)
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => filled($state))
                    ->afterStateHydrated(fn ($state, $record) => $state = null)
                    ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
        ]);
    }
}
