<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class UserController extends Controller
{
    protected $database;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path('firebase_config.json'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->database = $firebase->createDatabase();
    }

    public function registerForm()
    {
        return view('user.register');
    }

    public function register(Request $request)
    {
        $userId = uniqid();
        $this->database->getReference('users/'.$userId)->set([
            'name' => $request->name,
            'email' => $request->email,
            'balance' => 0,
            'face_id' => null
        ]);

        return redirect('/user/scan?uid='.$userId);
    }

    public function scanFace(Request $request)
    {
        $uid = $request->query('uid');
        return view('user.scanface', compact('uid'));
    }
}
