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
            ->login()
            ->profile(\App\Filament\Pages\CustomProfile::class)
            ->emailVerification()
            ->locale('en')
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
                        NavigationItem::make('Dashboard')
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
                        NavigationItem::make('Dashboard')
                            ->icon('heroicon-o-home')
                            ->url('/admin'),
                        NavigationItem::make('Absensi Saya')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->url('/admin/my-attendance'),
                    ]);
                }

                // MENU ADMIN (default)
                return $builder->items([
                    NavigationItem::make('Dashboard')
                        ->icon('heroicon-o-home')
                        ->url('/admin'),
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
                    NavigationItem::make('Kelola Admin')
                        ->icon('heroicon-o-shield-check')
                        ->url('/admin/admins'),
                ]);
            })
            ->renderHook('panels::footer', fn () => view('filament.footer'))
            ->renderHook('panels::auth.login.form.before', fn () => view('filament.login-header'))
            ->renderHook('panels::styles.before', fn () => new \Illuminate\Support\HtmlString('
                <style>
                    /* Hide default login heading */
                    .fi-simple-page h2.fi-simple-page-heading {
                        display: none;
                    }
                    /* Adjust login form spacing */
                    .fi-simple-page form {
                        margin-top: -1rem;
                    }
                    /* Mobile responsive footer */
                    @media (max-width: 640px) {
                        footer .flex {
                            flex-direction: column;
                            gap: 0.5rem;
                        }
                        footer .flex-1 {
                            width: 100%;
                            text-align: center !important;
                        }
                    }
                </style>
            '))
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
                ->subject('Verify Email Address')
                ->line('Please click the button below to verify your email address.')
                ->action('Verify Email Address', $url)
                ->line('If you did not create an account, no further action is required.');
        });
    }
}