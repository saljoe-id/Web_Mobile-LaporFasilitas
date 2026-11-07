<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_petugas_can_open_the_filament_dashboard(): void
    {
        foreach ([User::factory()->admin()->create(), User::factory()->petugas()->create()] as $user) {
            $this->actingAs($user)->get('/admin')->assertOk()->assertSee('Dashboard');
        }
    }

    public function test_inactive_or_non_staff_users_cannot_access_the_admin_panel(): void
    {
        $inactiveStaff = User::factory()->petugas()->inactive()->create();
        $this->actingAs($inactiveStaff)->get('/admin')->assertForbidden();
    }

    public function test_petugas_cannot_view_or_manage_admin_accounts(): void
    {
        $petugas = User::factory()->petugas()->create();
        $admin = User::factory()->admin()->create();

        $this->assertFalse(Gate::forUser($petugas)->allows('view', $admin));
        $this->assertFalse(Gate::forUser($petugas)->allows('update', $admin));
        $this->assertFalse(Gate::forUser($petugas)->allows('delete', $admin));
        $this->assertTrue(Gate::forUser($petugas)->allows('update', $petugas));

        $this->actingAs($petugas)
            ->get('/admin/users/'.$admin->id.'/edit')->assertNotFound();
        $this->get('/admin/categories/create')->assertForbidden();
    }

    public function test_petugas_user_list_query_excludes_admin_accounts(): void
    {
        $petugas = User::factory()->petugas()->create();
        $admin = User::factory()->admin()->create();
        $this->actingAs($petugas);

        $visibleUserIds = UserResource::getEloquentQuery()->pluck('id')->all();

        $this->assertContains($petugas->id, $visibleUserIds);
        $this->assertNotContains($admin->id, $visibleUserIds);
    }

    public function test_admin_resources_and_complaint_detail_render_from_database_records(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $complaint = Complaint::factory()->create(['category_id' => $category->id]);
        $assignment = Assignment::factory()->create(['complaint_id' => $complaint->id]);
        $this->actingAs($admin);

        $this->get('/admin/complaints')->assertOk()->assertSee($complaint->complaint_code);
        $this->get('/admin/complaints/'.$complaint->id)->assertOk()->assertSee($complaint->title);
        $this->get('/admin/categories')->assertOk()->assertSee($category->name);
        $this->get('/admin/categories/create')->assertOk();
        $this->get('/admin/users')->assertOk()->assertSee($admin->email);
        $this->get('/admin/users/create')->assertOk();
        $this->get('/admin/users/'.$admin->id.'/edit')->assertOk();
        $this->get('/admin/assignments')->assertOk()->assertSee($assignment->user->name);
    }

    public function test_petugas_cannot_create_an_admin_account_by_tampering_with_the_role(): void
    {
        $petugas = User::factory()->petugas()->create();
        $this->actingAs($petugas);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Akun Admin Palsu',
                'email' => 'admin-palsu@example.test',
                'role' => UserRole::Admin->value,
                'is_active' => true,
                'password' => 'KataSandiAman123!',
                'password_confirmation' => 'KataSandiAman123!',
            ])
            ->call('create')
            ->assertHasFormErrors(['role' => 'in']);

        $this->assertDatabaseMissing('users', ['email' => 'admin-palsu@example.test']);
    }
}
