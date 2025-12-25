<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'author',
        'year',
        'category_id',
        'description',
        'stock',
        'digital_link',
        'stock_online',
        'floor',
        'shelf_code',
        'cover'
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

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1);
    }
}
