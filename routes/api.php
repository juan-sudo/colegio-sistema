<?php

use App\Http\Controllers\Api\BiometricController;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->post("/biometric/marcar", [BiometricController::class, "registrar"]);
