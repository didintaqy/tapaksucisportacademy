<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __invoke(Request $request)
    {
        $page  = 'Kontak Kami';
        return view('frontend.contact-us', compact('page'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create($validated);

        return redirect()->route('contact-us')
            ->with('success', 'Pesan Anda telah terkirim. Kami akan segera merespons.');
    }
}