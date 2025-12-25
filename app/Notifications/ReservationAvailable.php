<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;

class ReservationAvailable extends Notification
{
    use Queueable;
    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('📚 Buku Pesanan Tersedia!')
            ->greeting('Halo, ' . $notifiable->name)
            ->line('Kabar baik! Admin Perpustakaan telah memproses antrian Anda.')
            ->line('Buku: **' . $this->reservation->book->title . '**')
            ->line('Status saat ini: SIAP DIAMBIL (Available).')
            ->line('Silakan datang ke perpustakaan dalam waktu 2x24 jam untuk mengambil buku tersebut sebelum antrian hangus.')
            ->action('Lihat Profil Saya', route('profile'))
            ->line('Terima kasih telah menggunakan layanan perpustakaan kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->id,
            'book_title' => $this->reservation->book->title,
            'message' => 'Admin telah menyiapkan buku "' . $this->reservation->book->title . '". Silakan ambil sekarang!',
            'type' => 'reservation_ready'
        ];
    }
}
