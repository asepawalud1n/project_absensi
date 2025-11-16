<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class PasswordChangedNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $time = Carbon::now('Asia/Jakarta')->format('d M Y, H:i');
        
        return (new MailMessage)
            ->subject('Password Anda Telah Diubah')
            ->greeting('Halo!')
            ->line('Password akun Anda telah berhasil diubah pada ' . $time . ' WIB.')
            ->line('Jika Anda tidak melakukan perubahan ini, segera hubungi administrator.')
            ->line('Untuk keamanan, pastikan Anda login kembali dengan password baru Anda.')
            ->salutation('Terima kasih!');
    }
}