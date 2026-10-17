<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\Request;

class PaymentPortalController extends Controller
{
    public function __construct(protected PaymentGatewayManager $gateways) {}

    /**
     * Halaman awal portal: form input ID siswa.
     */
    public function index()
    {
        return view('frontend.payments.index', [
            'student' => $this->currentStudent(),
        ]);
    }

    /**
     * Cari siswa berdasarkan ID atau NIK, lalu simpan di session.
     */
    public function search(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'max:32'],
        ], [], [
            'student_id' => 'ID Siswa',
        ]);

        $keyword = trim($data['student_id']);

        $student = Student::query()
            ->where('id', $keyword)
            ->orWhere('nik', $keyword)
            ->first();

        if (! $student) {
            return back()
                ->withInput()
                ->with('error', 'Siswa dengan ID / NIK tersebut tidak ditemukan.');
        }

        session(['payment_portal_student_id' => $student->id]);

        return redirect()->route('payments.portal.bills');
    }

    /**
     * Menampilkan daftar tagihan siswa.
     */
    public function bills()
    {
        $student = $this->currentStudent();

        if (! $student) {
            return redirect()
                ->route('payments.portal')
                ->with('error', 'Silakan masukkan ID siswa terlebih dahulu.');
        }

        $payments = $student->payments()
            ->with('paymentType')
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.payments.bills', [
            'student' => $student,
            'payments' => $payments,
        ]);
    }

    /**
     * Membuat charge pembayaran melalui gateway, lalu arahkan ke halaman bayar.
     */
    public function checkout(Request $request, Payment $payment)
    {
        $student = $this->currentStudent();

        abort_unless($student && $payment->student_id === $student->id, 404);
        abort_unless($payment->status->isPayable() && ! $payment->isPaid(), 403, 'Tagihan ini tidak dapat dibayar.');

        $this->gateways->driver()->createCharge($payment);

        return redirect()->route('payments.portal.checkout.show', $payment);
    }

    /**
     * Halaman pembayaran (QRIS / instruksi).
     */
    public function checkoutShow(Payment $payment)
    {
        $student = $this->currentStudent();

        abort_unless($student && $payment->student_id === $student->id, 404);

        return view('frontend.payments.checkout', [
            'student' => $student,
            'payment' => $payment,
        ]);
    }

    /**
     * Kwitansi mandiri untuk orang tua (setelah pembayaran lunas).
     */
    public function receipt(Payment $payment)
    {
        $student = $this->currentStudent();

        abort_unless($student && $payment->student_id === $student->id, 404);
        abort_unless($payment->isPaid(), 403, 'Kwitansi tersedia setelah pembayaran lunas.');

        $payment->load(['student', 'paymentType', 'creator']);

        return view('receipts.payment', [
            'payment' => $payment,
        ]);
    }

    /**
     * Keluar dari mode portal (hapus session).
     */
    public function logout()
    {
        session()->forget('payment_portal_student_id');

        return redirect()->route('payments.portal');
    }

    protected function currentStudent(): ?Student
    {
        $id = session('payment_portal_student_id');

        return $id ? Student::find($id) : null;
    }
}
