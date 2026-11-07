<?php

namespace Tests\Feature;

use App\Enums\AssignmentStatus;
use App\Enums\ComplaintStatus;
use App\Models\Assignment;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dashboard_list_detail_and_action_screens_render(): void
    {
        $admin = User::factory()->admin()->create();
        $complaint = Complaint::factory()->create();
        $this->actingAs($admin);

        $this->get(route('staff.dashboard'))->assertOk()->assertSee('Selamat pagi');
        $this->get(route('staff.complaints.index'))->assertOk()->assertSee('Manajemen pengaduan');
        $this->get(route('staff.complaints.show', $complaint))->assertOk()->assertSee($complaint->reporter_name);
        $this->get(route('staff.complaints.action', [$complaint, 'verify']))->assertOk()->assertSee('Verifikasi laporan');
    }

    public function test_staff_can_verify_assign_process_and_complete_a_report(): void
    {
        $admin = User::factory()->admin()->create();
        $fieldOfficer = User::factory()->petugas()->create();
        $complaint = Complaint::factory()->create(['status' => ComplaintStatus::Pending]);
        $this->actingAs($admin);

        $this->post(route('staff.complaints.action.store', [$complaint, 'verify']))
            ->assertRedirect(route('staff.complaints.show', $complaint));
        $complaint->refresh();
        $this->assertSame(ComplaintStatus::Diverifikasi, $complaint->status);

        $this->post(route('staff.complaints.action.store', [$complaint, 'assign']), [
            'user_id' => $fieldOfficer->id,
            'notes' => 'Tim lapangan akan memeriksa lokasi.',
        ])->assertRedirect(route('staff.complaints.show', $complaint));

        $this->post(route('staff.complaints.action.store', [$complaint, 'progress']), [
            'notes' => 'Pemeriksaan lokasi sedang dilakukan.',
        ])->assertRedirect(route('staff.complaints.show', $complaint));
        $complaint->refresh();
        $this->assertSame(ComplaintStatus::Diproses, $complaint->status);

        $this->post(route('staff.complaints.action.store', [$complaint, 'complete']), [
            'notes' => 'Lampu sudah diperbaiki dan kembali menyala.',
        ])->assertRedirect(route('staff.complaints.show', $complaint));
        $complaint->refresh();

        $this->assertSame(ComplaintStatus::Selesai, $complaint->status);
        $this->assertSame(AssignmentStatus::Selesai, Assignment::query()->firstOrFail()->status);
        $this->assertDatabaseCount('complaint_status_histories', 3);
    }

    public function test_staff_pages_require_an_authenticated_staff_account(): void
    {
        $this->get(route('staff.dashboard'))->assertRedirect(route('staff.login'));
    }
}
