<?php

// Aturan sirkulasi. Diletakkan di satu tempat supaya angka yang tampil di UI
// dan angka yang dipakai App\Services\Circulation selalu sama.
return [
    'loan_days' => (int) env('LIBRARY_LOAN_DAYS', 7),
    'ebook_days' => (int) env('LIBRARY_EBOOK_DAYS', 3),
    'renew_days' => (int) env('LIBRARY_RENEW_DAYS', 7),
    'max_renewals' => 1,

    // Tiket pinjam biasa harus diambil dalam 24 jam, tiket dari antrean 48 jam.
    'pickup_hours' => 24,
    'reservation_pickup_hours' => 48,

    'daily_request_limit' => 3,
    'max_active_reservations' => 2,

    'fine_per_day' => (int) env('LIBRARY_FINE_PER_DAY', 1000),
];
