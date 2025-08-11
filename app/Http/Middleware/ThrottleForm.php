<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Log;

class ThrottleForm extends ThrottleRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 5, $decayMinutes = 1, $prefix = '')
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            Log::channel('malicious_log')->info("Potential brute-force attack detected from IP: " . $request->ip());
            return response()->json(['message' => 'Too many attempts. Please try again later.'], 429);
        }

        $response = parent::handle($request, $next, $maxAttempts, $decayMinutes);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }
}
