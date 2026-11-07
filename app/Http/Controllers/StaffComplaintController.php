<?php

namespace App\Http\Controllers;

use App\Enums\AssignmentStatus;
use App\Enums\ComplaintStatus;
use App\Enums\UserRole;
use App\Http\Requests\StaffComplaintActionRequest;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,baru,diverifikasi,diproses,selesai,ditolak'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);
        $complaints = Complaint::query()
            ->with('category')
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('complaint_code', 'like', "%{$term}%")
                ->orWhere('title', 'like', "%{$term}%")))
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category_id', $category))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('staff.complaints.index', [
            'complaints' => $complaints,
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load(['category', 'statusHistories.user', 'assignments.user', 'activeAssignment.user']);

        return view('staff.complaints.show', compact('complaint'));
    }

    public function action(Complaint $complaint, string $action): View
    {
        abort_unless(in_array($action, ['verify', 'reject', 'assign', 'progress', 'complete'], true), 404);

        return view('staff.complaints.action', [
            'complaint' => $complaint->load('category'),
            'action' => $action,
            'staffUsers' => $action === 'assign'
                ? User::query()->where('role', UserRole::Petugas->value)->where('is_active', true)->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function storeAction(StaffComplaintActionRequest $request, Complaint $complaint, string $action): RedirectResponse
    {
        abort_unless(in_array($action, ['verify', 'reject', 'assign', 'progress', 'complete'], true), 404);
        $notes = $request->validated('notes');

        if ($action === 'assign') {
            abort_unless($complaint->status === ComplaintStatus::Diverifikasi, 422, 'Verifikasi laporan sebelum membuat penugasan.');

            DB::transaction(function () use ($complaint, $request, $notes): void {
                $complaint->activeAssignment()->update([
                    'status' => AssignmentStatus::Dibatalkan,
                    'completed_at' => now(),
                ]);

                Assignment::query()->create([
                    'complaint_id' => $complaint->id,
                    'user_id' => $request->validated('user_id'),
                    'assigned_by_id' => $request->user()->id,
                    'assigned_at' => now(),
                    'status' => AssignmentStatus::Ditugaskan,
                    'notes' => $notes,
                ]);
            });

            return redirect()->route('staff.complaints.show', $complaint)->with('success', 'Penugasan tersimpan.');
        }

        $newStatus = match ($action) {
            'verify' => ComplaintStatus::Diverifikasi,
            'reject' => ComplaintStatus::Ditolak,
            'progress' => ComplaintStatus::Diproses,
            'complete' => ComplaintStatus::Selesai,
        };
        abort_unless($complaint->status->canTransitionTo($newStatus), 422, 'Status laporan tidak dapat diubah ke tahap tersebut.');
        abort_unless(
            ! in_array($action, ['progress', 'complete'], true) || $complaint->activeAssignment()->exists(),
            422,
            'Laporan harus memiliki petugas aktif sebelum diproses.',
        );

        DB::transaction(function () use ($complaint, $newStatus, $notes, $action): void {
            $oldStatus = $complaint->status;
            $complaint->update(['status' => $newStatus]);
            $complaint->statusHistories()->create([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => request()->user()->id,
                'notes' => $notes,
            ]);

            if ($action === 'reject') {
                $complaint->activeAssignment()->update([
                    'status' => AssignmentStatus::Ditolak,
                    'completed_at' => now(),
                    'notes' => $notes,
                ]);
            }

            if ($action === 'progress') {
                $complaint->activeAssignment()->update(['status' => AssignmentStatus::Diproses, 'notes' => $notes]);
            }

            if ($action === 'complete') {
                $complaint->activeAssignment()->update([
                    'status' => AssignmentStatus::Selesai,
                    'completed_at' => now(),
                    'notes' => $notes,
                ]);
            }
        });

        return redirect()->route('staff.complaints.show', $complaint)->with('success', 'Status laporan diperbarui.');
    }
}
