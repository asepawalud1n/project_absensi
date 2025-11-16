<x-filament-panels::page.simple>
    <x-filament-panels::form wire:submit="authenticate">
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/binusa.png') }}"
                 alt="Logo SMK Bintang Nusantara"
                 class="w-24 h-24 sm:w-32 sm:h-32 object-contain mb-4">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 dark:text-white text-center">
                SMK BINTANG NUSANTARA
            </h1>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-3 text-center">
                Masuk ke akun Anda
            </p>
        </div>

        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>
