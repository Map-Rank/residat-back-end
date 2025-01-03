<?php

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Routing\Route;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Messaging::class, function ($app) {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.projects.app.credentials'))
                ->withDatabaseUri(config('firebase.projects.app.database.url'));

            return $factory->createMessaging();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($data = [], $message = '', $status_code = 200) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => $message
            ], $status_code);
        });

        Response::macro('errors', function ($data = [], $message = '', $status_code = 500) {
            return response()->json([
                'status' => false,
                "errors" => $data,
                'message' => $message,
            ], $status_code);
        });

        Response::macro('notFoundId',
        function ($message=null) {
            // Log::warning(Route::currentRouteAction()." ID not found");
            return response()->json([
                'status' => false,
                'message' => $message ?? 'ID not found',
            ], 404);
        });

        Response::macro('notFound',
        function ($message=null) {
            // Log::warning(Route::currentRouteAction()." failed, Not found");
            return response()->json([
                'status' => false,
                'message' => $message ?? 'Resource not found',
            ], 404);
        });
    }

}
