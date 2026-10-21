<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Halaman autentikasi (tampilan saja, belum terhubung ke proses login)
Route::view('/login', 'auth.login')->name('login');

// Rute per role: setiap role punya file sendiri (routes/unit.php, nanti routes/wadir.php, dst.)
require __DIR__.'/unit.php';
