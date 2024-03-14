// app/Http/Middleware/Cors.php
<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowedOrigins = [
            'https://dev.residat.com/*', // Replace with your Vue app's domain
            // Add other allowed origins if needed
        ];

        if (in_array($request->server('HTTP_ORIGIN'), $allowedOrigins)) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', $request->server('HTTP_ORIGIN'))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Authorization, X-Requested-With');
        }

        return response()->json('Unauthorized', 401);
    }
}
