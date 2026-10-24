<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactFormReceivedMail;
use App\Models\ContactMessage;

class ContactUsController extends Controller
{
    public function __invoke()
    {
        $page = 'Kontak Kami';
        $breadcrumb = 'Kontak Kami';

        return view('frontend.contact-us', compact('page', 'breadcrumb'));
    }

    public function store(StoreContactMessageRequest $request)
    {
        $message = ContactMessage::create($request->safe()->except('website'));

        \Mail::to(config('mail.from.address'))
            ->queue(new ContactFormReceivedMail($message));

        return redirect()->route('contact-us')
            ->with('success', 'Pesan Anda telah terkirim. Kami akan segera merespons.');
    }
}
