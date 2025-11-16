<?php

use Illuminate\Support\Facades\Route;

// Redirect domain utama ke admin panel
Route::redirect('/', '/admin');
