<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreditsController extends Controller
{
    public function index(Request $request)
    {
        return view('credits', [
            'credits' => $request->user()->credits,
            'plans' => config('credits.plans')
        ]);
    }
}
