<?php

namespace Tests;

use App\Models\Book;
use App\Models\Category;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private int $seq = 0;

    protected function student(array $attrs = []): Student
    {
        $n = ++$this->seq;

        return Student::create($attrs + [
            'nim' => '9900'.str_pad((string) $n, 4, '0', STR_PAD_LEFT),
            'name' => "Mahasiswa {$n}",
            'email' => "mhs{$n}@test.local",
            'password' => 'password123',
            'jurusan' => 'Teknik Informatika',
            'gender' => 'P',
            'verification_status' => 'verified',
        ]);
    }

    protected function book(array $attrs = []): Book
    {
        $category = Category::firstOrCreate(['name' => 'Umum']);

        return Book::create($attrs + [
            'title' => 'Buku '.(++$this->seq),
            'author' => 'Penulis',
            'year' => 2020,
            'category_id' => $category->id,
            'stock' => 1,
            'stock_online' => 0,
            'floor' => 1,
            'shelf_code' => '000 TES',
        ]);
    }

    protected function admin(): User
    {
        return User::firstOrCreate(['email' => 'admin@test.local'], ['name' => 'Admin', 'password' => 'password123']);
    }
}
