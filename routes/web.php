<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationController;

Route::get('/organizations', [OrganizationController::class, 'index']);
Route::get('/organizations/all', [OrganizationController::class, 'all']);

Route::get('/', function () {
    return view('welcome');
});
