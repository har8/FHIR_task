<?php

namespace App\Providers;

use App\Contracts\IFhirService;
use App\Services\CernerService;
use App\Services\SmartService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

class DiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(IFhirService::class, function (Application $app) {

            $currentController = $this->getCurrentController();

            switch ($currentController) {
                case 'SmartFhirController':
                    return new SmartService();
                case 'CernerFhirController':
                    return new CernerService();
                default:
                    throw new \Exception("Undefined controller: $currentController");
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    private function getCurrentController()
    {
        $routeArray = Str::parseCallback(\Route::currentRouteAction(), null);

        if (last($routeArray) != null) {
            $controller = class_basename(head($routeArray));

            return $controller;
        }

        return '';
    }
}
