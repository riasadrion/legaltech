<?php

namespace App\Http\Controllers\Frontend;

use App\Domain\Subscription\Models\Plan;
use App\Http\Controllers\Controller;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->orderBy('sort')->get();

        return view('theme::plans.index', compact('plans'));
    }
}
