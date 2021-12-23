<?php

use Illuminate\Support\Facades\Route;

$middleware = config('starter-kit.api_guard');

Route::prefix('api')->middleware($middleware)->group(function () {
    //
});
