<?php

use Illuminate\Support\Facades\Route;

Route::get('/optimize', function () {
    return Artisan::call('optimize:clear');
});
