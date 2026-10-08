<?php

namespace App\Exceptions;

use RuntimeException;

/** Aturan sirkulasi yang dilanggar. Pesannya ditampilkan apa adanya ke pengguna. */
class CirculationException extends RuntimeException {}
