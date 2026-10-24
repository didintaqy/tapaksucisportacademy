<?php

namespace App\Http\Controllers;

use App\Enums\TestimonialStatus;
use App\Http\Requests\StoreTestimonialRequest;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::query()
            ->approved()
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->get();

        $page = 'Testimoni';
        $breadcrumb = 'Testimoni';

        return view('frontend.testimonials', compact('testimonials', 'page', 'breadcrumb'));
    }

    public function store(StoreTestimonialRequest $request)
    {
        Testimonial::create([
            ...$request->safe()->except('website'),
            'status' => TestimonialStatus::Pending,
        ]);

        return redirect()->route('testimonials')
            ->with('success', 'Terima kasih! Testimoni Anda akan tampil setelah disetujui admin.');
    }
}
