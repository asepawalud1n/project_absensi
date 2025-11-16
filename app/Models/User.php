<?php

namespace App\Models;

use App\Notifications\PasswordChangedNotification;
use Filament\Facades\Filament;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User bisa jadi Guru
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Relationship: User bisa jadi Siswa
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Send email verification notification (Bahasa Indonesia).
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmail {
            public function toMail($notifiable): MailMessage
            {
                // Generate verification URL for Filament
                $verificationUrl = URL::temporarySignedRoute(
                    'filament.admin.auth.email-verification.verify',
                    now()->addMinutes(60),
                    [
                        'id' => $notifiable->getKey(),
                        'hash' => sha1($notifiable->getEmailForVerification()),
                    ]
                );

                return (new MailMessage)
                    ->subject('Verifikasi Alamat Email')
                    ->line('Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.')
                    ->action('Verifikasi Alamat Email', $verificationUrl)
                    ->line('Jika Anda tidak membuat akun, abaikan email ini.');
            }
        });
    }

    /**
     * Auto send email verification when email changed & password changed notification.
     */
    protected static function booted()
    {
        static::updating(function ($user) {
            // Cek apakah email berubah
            if ($user->isDirty('email')) {
                // Reset email verified status
                $user->email_verified_at = null;
            }
        });

        static::updated(function ($user) {
            // Jika email berubah, kirim email verification otomatis
            if ($user->wasChanged('email')) {
                $user->sendEmailVerificationNotification();
            }

            // Jika password berubah, kirim email notifikasi
            if ($user->wasChanged('password')) {
                $user->notify(new PasswordChangedNotification());
            }
        });
    }
}
