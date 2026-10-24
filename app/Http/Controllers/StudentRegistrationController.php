<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRegistrationRequest;
use App\Mail\RegistrationReceivedMail;
use App\Models\Program;
use App\Models\StudentRegistration;

class StudentRegistrationController extends Controller
{
    public function index()
    {
        $programs = Program::query()->active()->ordered()->get();
        $page = 'Pendaftaran Siswa';
        $breadcrumb = 'Pendaftaran';

        return view('frontend.registration', compact('programs', 'page', 'breadcrumb'));
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
