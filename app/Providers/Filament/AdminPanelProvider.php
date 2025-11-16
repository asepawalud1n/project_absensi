<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->profile(\App\Filament\Pages\CustomProfile::class)
            ->emailVerification()
            ->brandLogo(fn () => view('filament.sidebar-brand'))
            ->brandName('')
            ->colors([
                'primary' => Color::Blue,
                'danger'  => Color::Red,
                'gray'    => Color::Slate,
                'info'    => Color::Sky,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->favicon(asset('favicon.ico'))
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $user = auth()->user();

                // MENU GURU
                if ($user && $user->role === 'guru') {
                    return $builder->items([
                        NavigationItem::make('Dasbor')
                            ->icon('heroicon-o-home')
                            ->url('/admin'),
                        NavigationItem::make('Data Siswa')
                            ->icon('heroicon-o-users')
                            ->url('/admin/students'),
                        NavigationItem::make('Kelola Absen Siswa')
                            ->icon('heroicon-o-clipboard-document')
                            ->url('/admin/attendances'),
                        NavigationItem::make('Rekap Absen Siswa')
                            ->icon('heroicon-o-chart-bar')
                            ->url('/admin/attendance-recap'),
                    ]);
                }

                // MENU SISWA
                if ($user && $user->role === 'siswa') {
                    return $builder->items([
                        NavigationItem::make('Dasbor')
                            ->icon('heroicon-o-home')
                            ->url('/admin'),
                        NavigationItem::make('Absensi Saya')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->url('/admin/my-attendance'),
                    ]);
                }

                // MENU ADMIN (default)
                return $builder->items([
                    NavigationItem::make('Dasbor')
                        ->icon('heroicon-o-home')
                        ->url('/admin'),
                    NavigationItem::make('Kelola Admin') 
                        ->icon('heroicon-o-shield-check')
                        ->url('/admin/admins'),
                    NavigationItem::make('Data Guru')
                        ->icon('heroicon-o-user-group')
                        ->url('/admin/teachers'),
                    NavigationItem::make('Data Kelas')
                        ->icon('heroicon-o-academic-cap')
                        ->url('/admin/school-classes'),
                    NavigationItem::make('Data Siswa')
                        ->icon('heroicon-o-users')
                        ->url('/admin/students'),
                    NavigationItem::make('Data Jurusan')
                        ->icon('heroicon-o-briefcase')
                        ->url('/admin/majors'),
                    NavigationItem::make('Rekap Absen')
                        ->icon('heroicon-o-chart-bar')
                        ->url('/admin/attendance-recap'),
                ]);
            })
            ->renderHook('panels::footer', fn () => view('filament.footer'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\EnsureAuthenticated::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->font('Inter');
    }

    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email')
                ->line('Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Alamat Email', $url)
                ->line('Jika Anda tidak membuat akun, abaikan email ini.');
        });
    }
}