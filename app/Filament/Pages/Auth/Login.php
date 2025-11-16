<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent()
                            ->label('Email'),
                        $this->getPasswordFormComponent()
                            ->label('Kata Sandi'),
                        $this->getRememberFormComponent()
                            ->label('Ingat saya'),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getAuthenticateFormAction(): Component
    {
        return parent::getAuthenticateFormAction()
            ->label('Masuk');
    }
}
