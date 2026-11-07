<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_is_cast_and_users_are_active_by_default(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertSame(UserRole::Petugas, $petugas->role);
        $this->assertTrue($admin->is_active);
    }

    public function test_staff_fields_are_fillable_and_password_is_hashed(): void
    {
        $user = User::factory()->create([
            'instansi' => 'Dinas PUPR',
            'phone_number' => '081234567890',
            'password' => 'plain-password',
        ]);

        $this->assertSame('Dinas PUPR', $user->instansi);
        $this->assertSame('081234567890', $user->phone_number);
        $this->assertNotSame('plain-password', $user->password);
    }

    public function test_active_staff_can_access_the_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $this->assertTrue(User::factory()->admin()->create()->canAccessPanel($panel));
        $this->assertTrue(User::factory()->petugas()->create()->canAccessPanel($panel));
    }

    public function test_inactive_staff_cannot_access_the_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');

        $this->assertFalse(User::factory()->admin()->inactive()->create()->canAccessPanel($panel));
    }
}
