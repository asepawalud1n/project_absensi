<x-filament-panels::page.simple>
    <style>
        /* Minimalist Login Form Styles */
        .fi-simple-main {
            max-width: 400px !important;
            padding: 1.5rem !important;
        }

        .fi-simple-main-ctn {
            padding: 1.5rem !important;
        }

        /* Make the form container smaller */
        .fi-fo-component-ctn {
            padding: 0.75rem 0 !important;
        }

        /* Reduce spacing between fields */
        .fi-fo-field-wrp-label {
            margin-bottom: 0.375rem !important;
        }

        /* Make inputs more compact */
        .fi-input {
            padding: 0.5rem 0.75rem !important;
        }

        /* Reduce button size */
        .fi-btn {
            padding: 0.5rem 1rem !important;
        }

        /* Make the logo/brand smaller */
        .fi-simple-header {
            margin-bottom: 1rem !important;
        }

        /* Reduce heading size */
        .fi-simple-heading {
            font-size: 1.25rem !important;
            margin-bottom: 0.5rem !important;
        }
    </style>

    @if (filament()->hasLogin())
        <x-slot name="heading">
            Login
        </x-slot>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
    @endif
</x-filament-panels::page.simple>
