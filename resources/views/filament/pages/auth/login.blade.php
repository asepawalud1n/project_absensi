<x-filament-panels::page.simple>
    @if (filament()->hasLogin())
        <x-slot name="heading">
            <div class="flex flex-col items-center w-full">
                <img src="{{ asset('images/binusa.png') }}"
                     alt="Logo SMK Bintang Nusantara"
                     class="w-20 h-20 sm:w-28 sm:h-28 md:w-32 md:h-32 object-contain mb-4">
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white text-center">
                    SMK BINTANG NUSANTARA
                </h1>
            </div>
        </x-slot>

        <x-slot name="subheading">
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center mt-2">
                Masuk ke akun Anda
            </p>
        </x-slot>
    @endif

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>
