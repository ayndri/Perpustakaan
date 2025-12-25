<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Kepala Perpustakaan',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('password123'),
        ]);

        Student::create([
            'nim' => '20250001',
            'name' => 'Budi Santoso',
            'email' => 'budi@mhs.com',
            'password' => Hash::make('password123'),
            'jurusan' => 'Teknik Informatika',
            'gender' => 'L'
        ]);

        Student::create([
            'nim' => '1462300065',
            'name' => 'Dewi Nur Ayundari',
            'email' => 'dewinurayundari.dna@gmail.com',
            'password' => Hash::make('password123'),
            'jurusan' => 'Teknik Informatika',
            'gender' => 'P'
        ]);

        $cat1 = Category::create(['name' => 'Pemrograman']);
        $cat2 = Category::create(['name' => 'Fiksi']);
        $cat3 = Category::create(['name' => 'Bisnis']);

        Book::create([
            'title' => 'Belajar Laravel 10',
            'author' => 'Taylor Otwell',
            'year' => 2023,
            'category_id' => $cat1->id,
            'stock' => 5,
            'stock_online' => 2,
            'digital_link' => 'https://laravel.com/docs',
            'description' => 'Buku panduan lengkap untuk menguasai Framework Laravel versi 10 dari dasar hingga mahir.'
        ]);

        Book::create([
            'title' => 'Harry Potter (Fisik Only)',
            'author' => 'J.K. Rowling',
            'year' => 2005,
            'category_id' => $cat2->id,
            'stock' => 3,
            'stock_online' => 2,
            'digital_link' => null,
            'description' => 'Novel fantasi best seller tentang penyihir muda Harry Potter.'
        ]);
    }
}
