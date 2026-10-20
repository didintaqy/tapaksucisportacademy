<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRegistrationRequest;
use App\Mail\RegistrationReceivedMail;
use App\Models\StudentRegistration;

class StudentRegistrationController extends Controller
{
    public function index()
    {
        $page = 'Pendaftaran Siswa';

        return view('frontend.registration', compact('page'));
    }

    public function store(StoreStudentRegistrationRequest $request)
    {
        $registration = StudentRegistration::create($request->safe()->except('website'));

        \Mail::to(config('mail.from.address'))
            ->queue(new RegistrationReceivedMail($registration));

        return redirect()->route('registration')
            ->with('success', 'Pendaftaran berhasil dikirim. Admin akan menghubungi Anda untuk proses selanjutnya.');
    }
}
