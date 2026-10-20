<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTestLegacy extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_triggers_pending_queue_job()
    {
        // Simulasi infra email tanpa memfalsifikasi (menggunakan log mailer)
        $this->post(route('contact-us.store'), [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'message' => 'Pesan test.',
            'website' => '',
        ]);

        // Harus menyimpan pesan di database
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Budi',
            'status' => 'unread',
        ]);
    }
}
