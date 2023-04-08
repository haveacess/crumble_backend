<?php

use App\Http\Controllers\Api\ImportController;
use Illuminate\Support\Facades\Route;

Route::post('/import/steam/{profileAlias}', [ImportController::class, 'addSteamAccount'])->where(['profileAlias' => '[a-zA-Z_\d]+']);
