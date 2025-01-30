<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ValidateOrderParameters
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
        $rank = $request->query('rank');
        $price = (float) $request->query('price');

        $ranks = Config::get('store.ranks', []);

        // Check if rank exists
        if (!isset($ranks[$rank])) {
            abort(400, 'Invalid rank selected');
        }

        // Verify price matches the configured price for the rank
        if (abs($ranks[$rank]['price'] - $price) > 0.01) { // Using 0.01 difference to handle floating point comparison
            abort(400, 'Invalid price for selected rank');
        }

        return $next($request);
    }
} 