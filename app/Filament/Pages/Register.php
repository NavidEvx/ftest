<?php

namespace App\Filament\Pages;

use App\Services\UserService;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Register extends Page
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.register';

    // -------- Form binding properties --------
    public ?string $name = null;
    public ?string $family_name = null;
    public ?string $email = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    protected static bool $shouldRegisterNavigation = false;


    public static function canAccess(): bool
    {
        return !auth()->check();
    }

    public function mount()
    {
        if (auth()->check()) {
            return redirect('/admin');
        }
    }

    public function getFormSchema(): array
    {
        return [
            TextInput::make('name')->label('Name')->required(),
            TextInput::make('family_name')->label('Family Name')->required(),
            TextInput::make('email')->label('Email')->email()->required(),
            TextInput::make('password')->label('Password')->password()->required()->minLength(8),
            TextInput::make('password_confirmation')->label('Confirm Password')->password()->required(),
        ];
    }

    public function submit(UserService $userService)
    {
        $data = $this->form->getState();

        try {
            // Register user via service (validation included)
            $user = $userService->register($data);

            // Success notification
            Notification::make()
                ->title('Registration successful!')
                ->success()
                ->send();

            // Login new user
            Auth::login($user);

            return redirect('/admin');
        } catch (ValidationException $e) {
            $errors = implode(' ', array_map(fn($v) => implode(' ', $v), $e->errors()));
            Notification::make()
                ->title('Registration process failed!')
                ->body($errors)
                ->danger()
                ->send();
        }
    }
}
