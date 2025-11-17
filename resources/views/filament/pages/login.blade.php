<x-filament-panels::page.simple>
    <style>
        .fi-simple-main {
            max-width: 400px !important;
        }

        .fi-simple-page {
            padding: 1rem !important;
        }

        @media (min-width: 768px) {
            .fi-simple-main {
                max-width: 380px !important;
            }
        }

        .fi-form-component-container {
            margin-bottom: 0.75rem !important;
        }

        .fi-input-wrp {
            padding: 0.5rem !important;
        }

        .fi-btn {
            padding: 0.5rem 1rem !important;
        }
    </style>

    @if (filament()->hasLogin())
        <x-slot name="heading">
            {{ __('filament-panels::pages/auth/login.heading') }}
        </x-slot>
    @endif

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.before') }}

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.login.form.after') }}
</x-filament-panels::page.simple>