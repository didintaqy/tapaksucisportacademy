<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $programs = Program::query()
            ->active()
            ->ordered()
            ->get();

        return view('frontend.home', compact('programs'));
    }
}
