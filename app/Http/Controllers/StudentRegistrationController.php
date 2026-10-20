<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRegistrationRequest;
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
        StudentRegistration::create($request->safe()->except('website'));

        return redirect()->route('registration')
            ->with('success', 'Pendaftaran berhasil dikirim. Admin akan menghubungi Anda untuk proses selanjutnya.');
    }
}
