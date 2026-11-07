<?php

namespace Tests\Feature;

use App\Enums\AssignmentStatus;
use App\Enums\ComplaintStatus;
use App\Filament\Admin\Resources\Complaints\Pages\ListComplaints;
use App\Models\Assignment;
use App\Models\Complaint;
use App\Models\User;
use App\Services\ComplaintWorkflow;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class ComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_and_rejection_save_status_history(): void
    {
        $admin = User::factory()->admin()->create();
        $pendingComplaint = Complaint::factory()->create(['status' => ComplaintStatus::Pending]);
        $workflow = app(ComplaintWorkflow::class);

        $workflow->verify($pendingComplaint, $admin);

        $this->assertSame(ComplaintStatus::Diverifikasi, $pendingComplaint->refresh()->status);
        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $pendingComplaint->id,
            'old_status' => ComplaintStatus::Pending->value,
            'new_status' => ComplaintStatus::Diverifikasi->value,
            'user_id' => $admin->id,
        ]);

        $workflow->reject($pendingComplaint, $admin, 'Laporan tidak sesuai kriteria fasilitas umum.');

        $this->assertSame(ComplaintStatus::Ditolak, $pendingComplaint->refresh()->status);
        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $pendingComplaint->id,
            'new_status' => ComplaintStatus::Ditolak->value,
            'notes' => 'Laporan tidak sesuai kriteria fasilitas umum.',
        ]);
    }

    public function test_assignment_progress_and_completion_update_related_records_transactionally(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->petugas()->create();
        $complaint = Complaint::factory()->create(['status' => ComplaintStatus::Diverifikasi]);
        $workflow = app(ComplaintWorkflow::class);

        $assignment = $workflow->assign($complaint, $staff, $admin, 'Periksa kondisi di lokasi.');
        $this->assertSame(AssignmentStatus::Ditugaskan, $assignment->status);
        $this->assertSame($staff->id, $assignment->user_id);

        $workflow->transition($complaint, $staff, ComplaintStatus::Diproses, 'Perbaikan sedang berlangsung.');
        $workflow->complete($complaint, $staff, 'Fasilitas sudah diperbaiki.');

        $this->assertSame(ComplaintStatus::Selesai, $complaint->refresh()->status);
        $this->assertSame(AssignmentStatus::Selesai, $assignment->refresh()->status);
        $this->assertNotNull($assignment->completed_at);
        $this->assertDatabaseCount('complaint_status_histories', 2);
        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $complaint->id,
            'new_status' => ComplaintStatus::Selesai->value,
            'user_id' => $staff->id,
            'notes' => 'Fasilitas sudah diperbaiki.',
        ]);
    }

    public function test_invalid_status_transition_does_not_change_complaint_or_create_history(): void
    {
        $staff = User::factory()->petugas()->create();
        $complaint = Complaint::factory()->create(['status' => ComplaintStatus::Pending]);

        try {
            app(ComplaintWorkflow::class)->transition($complaint, $staff, ComplaintStatus::Selesai);
            $this->fail('Transisi status yang tidak valid seharusnya ditolak.');
        } catch (ValidationException) {
            $this->assertSame(ComplaintStatus::Pending, $complaint->refresh()->status);
            $this->assertDatabaseCount('complaint_status_histories', 0);
        }
    }

    public function test_filament_verification_and_rejection_actions_apply_the_workflow(): void
    {
        $admin = User::factory()->admin()->create();
        $complaint = Complaint::factory()->create(['status' => ComplaintStatus::Pending]);
        $this->actingAs($admin);

        Livewire::test(ListComplaints::class)
            ->callAction(TestAction::make('verify')->table($complaint));

        $this->assertSame(ComplaintStatus::Diverifikasi, $complaint->refresh()->status);

        Livewire::test(ListComplaints::class)
            ->callAction(TestAction::make('reject')->table($complaint), [
                'reason' => 'Laporan tidak sesuai kriteria fasilitas umum.',
            ]);

        $this->assertSame(ComplaintStatus::Ditolak, $complaint->refresh()->status);
        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $complaint->id,
            'new_status' => ComplaintStatus::Ditolak->value,
            'notes' => 'Laporan tidak sesuai kriteria fasilitas umum.',
        ]);
    }

    public function test_filament_assignment_status_and_completion_actions_run_transactionally(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->petugas()->create();
        $complaint = Complaint::factory()->create(['status' => ComplaintStatus::Pending]);
        $this->actingAs($admin);

        Livewire::test(ListComplaints::class)
            ->callAction(TestAction::make('verify')->table($complaint));
        Livewire::test(ListComplaints::class)
            ->callAction(TestAction::make('assign')->table($complaint), [
                'user_id' => $staff->id,
                'notes' => 'Mohon periksa fasilitas di lokasi.',
            ]);
        Livewire::test(ListComplaints::class)
            ->callAction(TestAction::make('updateStatus')->table($complaint), [
                'status' => ComplaintStatus::Diproses->value,
                'notes' => 'Perbaikan dimulai.',
            ]);
        Livewire::test(ListComplaints::class)
            ->callAction(TestAction::make('complete')->table($complaint), [
                'notes' => 'Fasilitas telah selesai diperbaiki.',
            ]);

        $this->assertSame(ComplaintStatus::Selesai, $complaint->refresh()->status);
        $this->assertSame(AssignmentStatus::Selesai, Assignment::query()->firstOrFail()->status);
        $this->assertDatabaseCount('complaint_status_histories', 3);
    }
}
