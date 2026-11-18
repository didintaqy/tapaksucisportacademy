<?php

namespace App\Contracts;

interface WhatsAppGateway
{
    /**
     * Nama driver (mis. "log", "fonnte").
     */
    public function name(): string;

    /**
     * Mengirim pesan WhatsApp. Mengembalikan true bila diterima provider.
     *
     * @param  string  $to  Nomor tujuan format internasional (mis. 62812...).
     * @param  string  $template  Nama template internal (opsional, untuk audit).
     */
    public function send(string $to, string $message, ?string $template = null): bool;
}
