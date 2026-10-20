<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submission_creates_message(): void
    {
        $response = $this->post(route('contact-us.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '081234567890',
            'message' => 'Test message content',
        ]);

        $response->assertRedirect(route('contact-us'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'status' => 'unread',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $response = $this->post(route('contact-us.store'), [
            'name' => '',
            'email' => 'invalid-email',
            'message' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_mark_as_read_changes_status(): void
    {
        $message = ContactMessage::factory()->unread()->create();

        $message->markAsRead();

        $this->assertEquals('read', $message->fresh()->status);
        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_mark_as_replied_changes_status(): void
    {
        $message = ContactMessage::factory()->read()->create();

        $message->markAsReplied();

        $this->assertEquals('replied', $message->fresh()->status);
        $this->assertNotNull($message->fresh()->replied_at);
    }
}
