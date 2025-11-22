<?php

namespace App\Providers;

use App\Models\Classroom;
use App\Policies\ClassroomPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Classroom::class => ClassroomPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}


