<x-filament-panels::page.simple>
    <div class="fi-simple-page">
        <section class="grid auto-cols-fr gap-y-6">
            {{-- Logo dan Nama Sekolah --}}
            <div class="flex flex-col items-center justify-center mb-4">
                <div class="flex items-center gap-3 mb-3">
                    <img
                        src="{{ asset('logo.png') }}"
                        alt="Logo SMK Bintang Nusantara"
                        class="h-20 w-auto"
                    />
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white text-center">
                    SMK Bintang Nusantara
                </h1>
                <h2 class="text-lg font-medium text-gray-600 dark:text-gray-400 mt-4 text-center">
                    Masuk
                </h2>
            </div>

            {{-- Form Login --}}
            <x-filament-panels::form wire:submit="authenticate">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </section>
    </div>

    <style>
        /* Kompak layout - kurangi spacing */
        .fi-simple-page {
            max-width: 28rem !important;
        }

        /* Atur spacing form */
        .fi-fo-field-wrp {
            margin-bottom: 1rem !important;
        }
    </style>
</x-filament-panels::page.simple>
