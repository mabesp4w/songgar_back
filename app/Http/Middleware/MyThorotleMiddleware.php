<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MyThorotleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->ip();
        $maxAttempts = 100;

        if (Cache::has($key)) {
            $attempts = Cache::increment($key);
        } else {
            Cache::put($key, 1, now()->addMinutes(1));
            $attempts = 1;
        }

        if ($attempts > $maxAttempts) {
            return response()->json(['message' => 'Wooii Terlalu banyak permintaan.'], 429);
        }

        return $next($request);
    }
}
