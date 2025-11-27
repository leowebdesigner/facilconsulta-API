<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\PatientRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentPatientRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\DoctorRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentDoctorRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\DoctorScheduleRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentDoctorScheduleRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AppointmentRepositoryInterface::class,
            \App\Repositories\Eloquent\EloquentAppointmentRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
