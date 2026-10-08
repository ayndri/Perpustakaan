<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publisher',
        'year',
        'category_id',
        'description',
        'stock',
        'digital_link',
        'stock_online',
        'floor',
        'shelf_code',
        'cover',
    ];

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class)->latest();
    }

    public function waitingReservations()
    {
        return $this->hasMany(Reservation::class)->where('status', 'waiting')->orderBy('created_at')->orderBy('id');
    }

    public function coverUrl(int $width = 400): ?string
    {
        return Media::url($this->cover, $width);
    }

    /** Label punggung buku, misalnya "Lt. 2 · 005.13 MAR". */
    public function shelfLabel(): ?string
    {
        if (! $this->shelf_code) {
            return null;
        }

        return 'Lt. '.($this->floor ?? 1).' · '.$this->shelf_code;
    }

    public function getAverageRatingAttribute()
    {
        $avg = $this->reviews_avg_rating ?? $this->reviews()->avg('rating');

        return $avg ? round($avg, 1) : null;
    }
}
