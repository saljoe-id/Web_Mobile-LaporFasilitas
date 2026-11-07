<?php

namespace Tests\Feature;

use App\Enums\AssignmentStatus;
use App\Enums\ComplaintStatus;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_complaint_has_relations_and_default_status(): void
    {
        $complaint = Complaint::factory()->create();

        $this->assertSame(ComplaintStatus::Pending, $complaint->status);
        $this->assertTrue($complaint->category->exists);
        $this->assertNull($complaint->activeAssignment);
    }

    public function test_reporter_identity_is_hidden_from_array(): void
    {
        $complaint = Complaint::factory()->create([
            'reporter_name' => 'Budi',
            'reporter_phone' => '081234567890',
            'reporter_email' => 'budi@example.test',
        ]);

        $array = $complaint->toArray();

        $this->assertArrayNotHasKey('reporter_name', $array);
        $this->assertArrayNotHasKey('reporter_phone', $array);
        $this->assertArrayNotHasKey('reporter_email', $array);
    }

    public function test_active_assignment_only_includes_unfinished_assignments(): void
    {
        $complaint = Complaint::factory()->create();
        Assignment::factory()->for($complaint)->create(['completed_at' => now()]);
        $active = Assignment::factory()->for($complaint)->create();

        $this->assertTrue($complaint->activeAssignment->is($active));
        $this->assertSame(AssignmentStatus::Ditugaskan, $active->status);
    }

    public function test_status_transitions_are_limited(): void
    {
        $this->assertTrue(ComplaintStatus::Baru->canTransitionTo(ComplaintStatus::Diverifikasi));
        $this->assertTrue(ComplaintStatus::Diverifikasi->canTransitionTo(ComplaintStatus::Diproses));
        $this->assertTrue(ComplaintStatus::Diproses->canTransitionTo(ComplaintStatus::Selesai));
        $this->assertTrue(ComplaintStatus::Diproses->canTransitionTo(ComplaintStatus::Ditolak));
        $this->assertFalse(ComplaintStatus::Baru->canTransitionTo(ComplaintStatus::Selesai));
        $this->assertFalse(ComplaintStatus::Selesai->canTransitionTo(ComplaintStatus::Diproses));
        $this->assertFalse(ComplaintStatus::Ditolak->canTransitionTo(ComplaintStatus::Selesai));
    }

    public function test_status_history_records_and_casts_statuses(): void
    {
        $complaint = Complaint::factory()->create();
        $history = ComplaintStatusHistory::factory()->for($complaint)->create([
            'old_status' => ComplaintStatus::Baru,
            'new_status' => ComplaintStatus::Diverifikasi,
        ]);

        $this->assertSame(ComplaintStatus::Diverifikasi, $history->new_status);
        $this->assertTrue($history->complaint->is($complaint));
    }

    public function test_category_slug_is_generated_from_name(): void
    {
        $category = Category::create(['name' => 'Penerangan Jalan']);

        $this->assertSame('penerangan-jalan', $category->slug);
    }
}
