<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('subscribe');
});

Route::get('/confirm', function () {
    $token = request()->query('token');
    return view('pages.confirm', ['token' => $token]);
});

Route::get('/unsubscribe', function () {
    $token = request()->query('token');
    return view('pages.unsubscribe', ['token' => $token]);
});

Route::fallback(function () {
    return response()->view('pages.404', [], 404);
});
