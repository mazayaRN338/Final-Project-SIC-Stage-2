<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class StoreController extends Controller
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();
    }

public function buyWithFace($id)
{
    // 1. Dapatkan event dari Firebase (UID siapa yang scan)
    $scannedUid = $this->database->getReference('currentScan')->getValue();
    if (!$scannedUid) return response('No face detected', 400);

    // 2. Dapatkan data user
    $user = $this->database->getReference('users/'.$scannedUid)->getValue();

    // 3. Dapatkan harga produk
    $product = [
        'id' => $id,
        'name' => 'Smart Plug',
        'price' => 150000
    ];

    if ($user['balance'] < $product['price']) {
        return response('Saldo tidak cukup', 400);
    }

    // 4. Update saldo user
    $newBalance = $user['balance'] - $product['price'];
    $this->database->getReference('users/'.$scannedUid.'/balance')->set($newBalance);

    // 5. Catat transaksi
    $this->database->getReference('transactions')->push([
        'user_id' => $scannedUid,
        'product' => $product['name'],
        'amount' => $product['price'],
        'time' => now()->toDateTimeString()
    ]);

    return response('Pembayaran berhasil', 200);
}

}