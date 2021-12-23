<?php

use Illuminate\Support\Facades\Route;

$middleware = config('starter-kit.web_guard');

Route::middleware($middleware)->group(function () {
    //
});
