<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'pickup_expires_at' => 'datetime',
        'handed_over_at' => 'datetime',
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
        'fine_paid_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'active']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('type', 'offline')
            ->where('due_at', '<', now());
    }

    public function scopeUnpaidFine(Builder $query): Builder
    {
        return $query->where('fine_amount', '>', 0)->whereNull('fine_paid_at');
    }

    public function isOffline(): bool
    {
        return $this->type === 'offline';
    }

    public function daysLate(?Carbon $at = null): int
    {
        if (! $this->isOffline() || ! $this->due_at) {
            return 0;
        }

        $at ??= $this->returned_at ?? now();

        return max(0, (int) ceil($this->due_at->diffInDays($at, false)));
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->daysLate() > 0;
    }

    /** Denda yang berlaku sekarang: terkunci kalau sudah kembali, berjalan kalau belum. */
    public function currentFine(): int
    {
        if ($this->status === 'returned') {
            return $this->fine_amount;
        }

        return $this->daysLate() * config('library.fine_per_day');
    }

    public function hasUnpaidFine(): bool
    {
        return $this->fine_amount > 0 && $this->fine_paid_at === null;
    }
}
