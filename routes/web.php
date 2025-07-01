<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadClientController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('lead-clients', LeadClientController::class);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/webhook/project-data/{project_name}', [LeadClientController::class, 'projectDataWebhook'])->name('project.webhook');