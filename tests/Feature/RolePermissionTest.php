<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Post;
use App\Models\Student;
use App\Models\StudentPerformance;
use App\Models\User;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(UserRole $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_all_roles_can_access_admin_panel(): void
    {
        $panel = Panel::make()->id('admin');

        foreach (UserRole::cases() as $role) {
            $this->assertTrue($this->userWithRole($role)->canAccessPanel($panel));
        }
    }

    public function test_only_admin_can_manage_users(): void
    {
        $admin = $this->userWithRole(UserRole::Admin);
        $operator = $this->userWithRole(UserRole::Operator);
        $coach = $this->userWithRole(UserRole::Pelatih);

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertFalse($operator->can('viewAny', User::class));
        $this->assertFalse($coach->can('viewAny', User::class));
    }

    public function test_coach_can_view_students_but_cannot_create_them(): void
    {
        $coach = $this->userWithRole(UserRole::Pelatih);
        $student = Student::factory()->create();

        $this->assertTrue($coach->can('viewAny', Student::class));
        $this->assertTrue($coach->can('view', $student));
        $this->assertFalse($coach->can('create', Student::class));
        $this->assertFalse($coach->can('delete', $student));
    }

    public function test_coach_can_manage_performances(): void
    {
        $coach = $this->userWithRole(UserRole::Pelatih);
        $performance = StudentPerformance::factory()->create();

        $this->assertTrue($coach->can('viewAny', StudentPerformance::class));
        $this->assertTrue($coach->can('create', StudentPerformance::class));
        $this->assertTrue($coach->can('update', $performance));
    }

    public function test_operator_can_manage_payments_but_cannot_delete_them(): void
    {
        $operator = $this->userWithRole(UserRole::Operator);
        $payment = Payment::factory()->create();

        $this->assertTrue($operator->can('viewAny', Payment::class));
        $this->assertTrue($operator->can('create', Payment::class));
        $this->assertTrue($operator->can('update', $payment));
        $this->assertFalse($operator->can('delete', $payment));
    }

    public function test_coach_cannot_access_payments_at_all(): void
    {
        $coach = $this->userWithRole(UserRole::Pelatih);

        $this->assertFalse($coach->can('viewAny', Payment::class));
        $this->assertFalse($coach->can('viewAny', PaymentType::class));
    }

    public function test_payment_types_are_admin_only_for_writes(): void
    {
        $admin = $this->userWithRole(UserRole::Admin);
        $operator = $this->userWithRole(UserRole::Operator);
        $type = PaymentType::factory()->create();

        $this->assertTrue($operator->can('viewAny', PaymentType::class));
        $this->assertFalse($operator->can('create', PaymentType::class));
        $this->assertTrue($admin->can('update', $type));
    }

    public function test_contact_messages_are_visible_to_admin_and_operator_only(): void
    {
        $admin = $this->userWithRole(UserRole::Admin);
        $operator = $this->userWithRole(UserRole::Operator);
        $coach = $this->userWithRole(UserRole::Pelatih);
        $message = ContactMessage::factory()->create();

        $this->assertTrue($admin->can('update', $message));
        $this->assertTrue($operator->can('update', $message));
        $this->assertFalse($operator->can('delete', $message));
        $this->assertFalse($coach->can('viewAny', ContactMessage::class));
    }

    public function test_content_management_is_limited_to_admin_and_operator(): void
    {
        $operator = $this->userWithRole(UserRole::Operator);
        $coach = $this->userWithRole(UserRole::Pelatih);

        $this->assertTrue($operator->can('create', Post::class));
        $this->assertTrue($operator->can('create', Gallery::class));
        $this->assertFalse($coach->can('viewAny', Post::class));
        $this->assertFalse($coach->can('viewAny', Gallery::class));
    }
}
