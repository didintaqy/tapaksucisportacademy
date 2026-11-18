<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Driver default tanpa provider eksternal.
 *
 * Pesan hanya ditulis ke log dan dicatat di `notification_logs`. Berguna untuk
 * pengembangan dan sebagai fallback bila kredensial provider belum diisi.
 */
class LogWhatsAppGateway implements WhatsAppGateway
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    public function name(): string
    {
        return 'log';
    }

    public function send(string $to, string $message, ?string $template = null): bool
    {
        $reference = 'log-'.Str::uuid()->toString();

        Log::info('WhatsApp (driver log)', [
            'to' => $to,
            'template' => $template,
            'message' => $message,
        ]);

        NotificationLog::create([
            'channel' => 'whatsapp',
            'recipient' => $to,
            'template' => $template,
            'status' => 'sent',
            'provider_ref' => $reference,
            'sent_at' => now(),
        ]);

        return true;
    }
}
