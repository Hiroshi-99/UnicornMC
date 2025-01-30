<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $ranks = Config::get('store.ranks', []);
        $rank = $request->input('rank');
        $price = (float) $request->input('price');

        if (!isset($ranks[$rank]) || abs($ranks[$rank]['price'] - $price) > 0.01) {
            return back()->with('error', 'Invalid order parameters');
        }

        // Continue with order processing...
    }
} 