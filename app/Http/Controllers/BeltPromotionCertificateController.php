<?php

namespace App\Http\Controllers;

use App\Enums\PromotionResult;
use App\Models\BeltPromotion;
use Illuminate\Support\Facades\Gate;

class BeltPromotionCertificateController extends Controller
{
    /**
     * Menampilkan sertifikat UKT yang siap dicetak / disimpan sebagai PDF
     * melalui fitur "Print to PDF" pada browser.
     */
    public function show(BeltPromotion $beltPromotion)
    {
        Gate::authorize('view', $beltPromotion);

        $beltPromotion->load(['student.program', 'examiner']);

        abort_unless(
            $beltPromotion->result === PromotionResult::Lulus && filled($beltPromotion->certificate_number),
            404,
            'Sertifikat belum diterbitkan.',
        );

        return view('certificates.belt-promotion', [
            'promotion' => $beltPromotion,
        ]);
    }
}
