<div class="fi-section-header-wrapper">
    <div class="fi-section-header">
        <div class="flex items-center justify-between gap-x-3">
            <div>
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Kelola Admin
                </h3>
                <p class="fi-section-header-description text-sm text-gray-500 dark:text-gray-400">
                    Kelola data administrator sistem
                </p>
            </div>

            <div class="fi-section-header-actions">
                @if (count($actions))
                    <div class="flex items-center gap-3">
                        @foreach ($actions as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mb-4"></div>
