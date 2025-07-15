<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadClientController;
 
Route::post('/outbound-call', [LeadClientController::class, 'outboundCall']); 