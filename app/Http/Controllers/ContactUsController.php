<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $page  = 'Kontak Kami';
        return view('frontend.contact-us', compact('page'));
    }
}
