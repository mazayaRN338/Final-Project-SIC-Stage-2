<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\FirebaseException;

class ListenFirebase extends Command
{
    protected $signature = 'firebase:listen';
    protected $description = 'Listen to Firebase currentScan node in real-time';

    public function handle()
    {
        $this->info("🚀 Listening to Firebase for face scans...");

        $firebase = (new Factory)
            ->withServiceAccount(base_path('firebase_config.json'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $database = $firebase->createDatabase();

        $ref = $database->getReference('currentScan');

        // Listener loop
        while (true) {
            $snapshot = $ref->getSnapshot();
            $data = $snapshot->getValue();

            if ($data && $data['status'] === 'recognized') {
                $uid = $data['uid'];
                $this->info("✅ Face recognized for UID: {$uid}");

                // proses pembayaran otomatis
                $this->processTransaction($database, $uid);

                // reset status agar tidak double transaksi
                $ref->set([
                    'uid' => null,
                    'status' => 'none',
                    'timestamp' => now()->toDateTimeString()
                ]);
            }

            sleep(2); // cek tiap 2 detik
        }
    }

    private function processTransaction($database, $uid)
    {
        $userRef = $database->getReference("users/{$uid}");
        $user = $userRef->getValue();

        if (!$user) {
            $this->warn("⚠️ User not found in Firebase.");
            return;
        }

        $price = 150000; // contoh harga produk tetap
        if ($user['balance'] < $price) {
            $this->warn("❌ Saldo tidak cukup untuk {$user['name']}.");
            return;
        }

        // Update saldo
        $newBalance = $user['balance'] - $price;
        $userRef->update(['balance' => $newBalance]);

        // Simpan transaksi
        $database->getReference('transactions')->push([
            'user_id' => $uid,
            'name' => $user['name'],
            'amount' => $price,
            'time' => now()->toDateTimeString()
        ]);

        $this->info("💰 Pembayaran berhasil untuk {$user['name']}. Sisa saldo: {$newBalance}");
    }
}
