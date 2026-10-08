<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Giliran antrean tiba: eksemplar sudah ditahan atas nama mahasiswa. */
class TicketReady extends Notification
{
    public function __construct(private Borrowing $ticket) {}

    public function via(object $notifiable): array
    {
        // Email hanya dikirim kalau mailer sungguhan dikonfigurasi; notifikasi di profil selalu ada.
        return in_array(config('mail.default'), ['log', 'array'], true)
            ? ['database']
            : ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $deadline = $this->ticket->pickup_expires_at->translatedFormat('l, j F Y \p\u\k\u\l H.i');

        return (new MailMessage)
            ->subject('Giliranmu: '.$this->ticket->book->title)
            ->greeting('Halo, '.$notifiable->name)
            ->line('Buku yang kamu antre sudah kami sisihkan atas namamu.')
            ->line('**'.$this->ticket->book->title.'**')
            ->line('Ambil di meja layanan sebelum '.$deadline.' dengan menunjukkan tiket '.$this->ticket->ticket_number.'. Lewat dari itu, eksemplarnya pindah ke antrean berikutnya.')
            ->action('Buka tiket', route('profile'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'borrowing_id' => $this->ticket->id,
            'book_title' => $this->ticket->book->title,
            'message' => 'Giliranmu tiba untuk "'.$this->ticket->book->title.'". Ambil sebelum '
                .$this->ticket->pickup_expires_at->translatedFormat('j M, H.i').'.',
        ];
    }
}
