<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'author',
        'publisher',
        'category',
        'reason',
        'status',
        'image'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
