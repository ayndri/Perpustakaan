<?php

namespace App\Exceptions;

use RuntimeException;

/** Gambar gagal disimpan. Pesannya ditampilkan ke pengguna; penyebab aslinya masuk log. */
class MediaException extends RuntimeException {}
