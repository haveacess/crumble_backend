<?php

use App\Http\Controllers\Web\ImportController;
use Illuminate\Support\Facades\Route;


Route::get('/import/steam/{accountAlias}', [ImportController::class, 'steamAccount']);
