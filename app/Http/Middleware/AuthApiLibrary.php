<?php

namespace App\Http\Middleware;

use App\Models\Library;
use Closure;
use Illuminate\Http\Request;

class AuthApiLibrary
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY'); // Assuming you pass the API key in the header

        if (!$apiKey) {
            return response()->json(['error' => 'API key is missing'], 401);
        }

        $library = Library::where('api_key', $apiKey)->first();

        if (!$library) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        // // Attach the authenticated user to the request for later use
        // $request->library = $library;

        return $next($request);
    }
}
