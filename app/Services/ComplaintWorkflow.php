<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ComplaintWorkflow
{
    public function verify(Complaint $complaint, User $actor): void
    {
        $this->transition($complaint, $actor, ComplaintStatus::Diverifikasi);
    }

    public function reject(Complaint $complaint, User $actor, string $reason): void
    {
        DB::transaction(function () use ($complaint, $actor, $reason): void {
            $lockedComplaint = Complaint::query()->lockForUpdate()->findOrFail($complaint->id);
            $this->changeStatus($lockedComplaint, $actor, ComplaintStatus::Ditolak, $reason);
        });
    }

    public function assign(Complaint $complaint, User $staff, User $actor, ?string $notes): Assignment
    {
        return DB::transaction(function () use ($complaint, $staff, $actor, $notes): Assignment {
            $lockedComplaint = Complaint::query()->lockForUpdate()->findOrFail($complaint->id);

            if ($staff->role !== UserRole::Petugas || ! $staff->is_active) {
                throw ValidationException::withMessages([
                    'user_id' => 'Pilih petugas aktif untuk menerima penugasan.',
                ]);
            }

            if (in_array($lockedComplaint->status, [ComplaintStatus::Selesai, ComplaintStatus::Ditolak], true)) {
                throw ValidationException::withMessages([
                    'user_id' => 'Pengaduan yang sudah selesai atau ditolak tidak dapat ditugaskan.',
                ]);
            }

            if ($lockedComplaint->activeAssignment()->exists()) {
                throw ValidationException::withMessages([
                    'user_id' => 'Pengaduan ini sudah memiliki petugas aktif.',
                ]);
            }

            return $lockedComplaint->assignments()->create([
                'user_id' => $staff->id,
                'assigned_by_id' => $actor->id,
                'assigned_at' => now(),
                'status' => AssignmentStatus::Ditugaskan,
                'notes' => $notes,
            ]);
        });
    }

    public function transition(Complaint $complaint, User $actor, ComplaintStatus $newStatus, ?string $notes = null): void
    {
        DB::transaction(function () use ($complaint, $actor, $newStatus, $notes): void {
            $lockedComplaint = Complaint::query()->lockForUpdate()->findOrFail($complaint->id);
            $this->changeStatus($lockedComplaint, $actor, $newStatus, $notes);
        });
    }

    public function complete(Complaint $complaint, User $actor, ?string $notes): void
    {
        DB::transaction(function () use ($complaint, $actor, $notes): void {
            $lockedComplaint = Complaint::query()->lockForUpdate()->findOrFail($complaint->id);
            $this->changeStatus($lockedComplaint, $actor, ComplaintStatus::Selesai, $notes);
        });
    }

    private function changeStatus(Complaint $complaint, User $actor, ComplaintStatus $newStatus, ?string $notes = null): void
    {
        if (! $complaint->status->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => 'Perubahan status tidak sesuai dengan alur penanganan pengaduan.',
            ]);
        }

        $oldStatus = $complaint->status;
        $complaint->update(['status' => $newStatus]);
        $complaint->statusHistories()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'user_id' => $actor->id,
            'notes' => $notes,
        ]);

        $assignmentStatus = match ($newStatus) {
            ComplaintStatus::Diproses => AssignmentStatus::Diproses,
            ComplaintStatus::Selesai => AssignmentStatus::Selesai,
            ComplaintStatus::Ditolak => AssignmentStatus::Ditolak,
            default => null,
        };

        if ($assignmentStatus !== null) {
            $activeAssignment = $complaint->activeAssignment()->first();

            if ($activeAssignment !== null) {
                $activeAssignment->update([
                    'status' => $assignmentStatus,
                    'completed_at' => in_array($newStatus, [ComplaintStatus::Selesai, ComplaintStatus::Ditolak], true) ? now() : null,
                ]);
            }
        }
    }
}
