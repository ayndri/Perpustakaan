<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'book_id', 'status', 'borrowing_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    /** Urutan di antrean, mulai dari 1. */
    public function position(): int
    {
        return static::where('book_id', $this->book_id)
            ->where('status', 'waiting')
            ->where(function ($q) {
                $q->where('created_at', '<', $this->created_at)
                    ->orWhere(fn ($q) => $q->where('created_at', $this->created_at)->where('id', '<', $this->id));
            })
            ->count() + 1;
    }
}
