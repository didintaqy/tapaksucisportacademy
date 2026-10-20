<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;

class ContactUsController extends Controller
{
    public function __invoke()
    {
        $page = 'Kontak Kami';

        return view('frontend.contact-us', compact('page'));
    }

    public function store(StoreContactMessageRequest $request)
    {
        ContactMessage::create($request->safe()->except('website'));

        return redirect()->route('contact-us')
            ->with('success', 'Pesan Anda telah terkirim. Kami akan segera merespons.');
    }
}
