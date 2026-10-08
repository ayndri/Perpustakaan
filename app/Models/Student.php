<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Support\Media;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $guard = 'student';

    protected $fillable = [
        'nim',
        'name',
        'email',
        'password',
        'jurusan',
        'ktm_image',
        'verification_status',
        'rejection_reason',
        'photo',
        'gender'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function bookRequests()
    {
        return $this->hasMany(BookRequest::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Book::class, 'favorites', 'student_id', 'book_id')->withTimestamps();
    }

    public function photoUrl(): ?string
    {
        return Media::url($this->photo, 200);
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /** Total denda yang belum dilunasi, termasuk denda berjalan dari buku yang masih telat. */
    public function outstandingFine(): int
    {
        $locked = (int) $this->borrowings()->unpaidFine()->sum('fine_amount');
        $running = $this->borrowings()->overdue()->get()->sum(fn ($b) => $b->currentFine());

        return $locked + $running;
    }
}
