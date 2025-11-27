<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DoctorController;
use App\Http\Controllers\Api\V1\HealthCheckController;
use App\Http\Controllers\Api\V1\PatientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('health', HealthCheckController::class)->name('health');

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::get('patient/profile', [PatientController::class, 'profile'])->name('patient.profile');
        Route::get('patient/appointments/upcoming', [PatientController::class, 'upcoming'])->name('patient.upcoming');

        Route::post('appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('appointments/doctor/{doctorId}', [AppointmentController::class, 'doctorAppointments'])->name('appointments.doctor');
        Route::get('appointments/patient/{patientId}', [AppointmentController::class, 'patientAppointments'])->name('appointments.patient');
        Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    });

    Route::get('doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('doctors/available', [DoctorController::class, 'available'])->name('doctors.available');
});
