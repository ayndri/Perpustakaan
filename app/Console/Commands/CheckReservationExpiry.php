<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckReservationExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-reservation-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredReservations = \App\Models\Reservation::where('status', 'available')
            ->where('updated_at', '<', now()->subDays(2))
            ->get();

        foreach ($expiredReservations as $res) {
            $res->update(['status' => 'cancelled']);
            $this->info("Reservasi ID {$res->id} dibatalkan karena kadaluarsa.");
        }
    }
}
