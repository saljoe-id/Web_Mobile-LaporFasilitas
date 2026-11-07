<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PublicTrackingController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:20'],
        ]);
        $code = trim($validated['code'] ?? '');
        $complaint = $code === ''
            ? null
            : Complaint::query()->with([
                'category',
                'statusHistories' => fn ($query) => $query->latest()->limit(1),
            ])->where('complaint_code', $code)->first();

        $statusSummary = $complaint ? $this->statusSummary($complaint) : null;

        return view('public.tracking.index', compact('code', 'complaint', 'statusSummary'));
    }

    public function show(string $code): View
    {
        $complaint = Complaint::query()
            ->with(['category', 'statusHistories' => fn ($query) => $query->oldest()])
            ->where('complaint_code', $code)
            ->firstOrFail();

        $statusSummary = $this->statusSummary($complaint);

        return view('public.tracking.show', compact('complaint', 'statusSummary'));
    }

    /** @return array{repair_label: string, duration_label: string, updated_at: Carbon|CarbonInterface} */
    private function statusSummary(Complaint $complaint): array
    {
        $lastHistory = $complaint->statusHistories->last();
        $resolvedHistory = $complaint->statusHistories->first(
            fn ($history): bool => $history->new_status === ComplaintStatus::Selesai,
        );
        $statusDate = $lastHistory?->created_at ?? $complaint->updated_at;
        $elapsedAt = match ($complaint->status) {
            ComplaintStatus::Selesai => $resolvedHistory?->created_at ?? $statusDate,
            ComplaintStatus::Ditolak => $statusDate,
            default => now(),
        };
        $elapsedDays = (int) $complaint->created_at->diffInDays($elapsedAt);

        return [
            'repair_label' => match ($complaint->status) {
                ComplaintStatus::Selesai => 'Sudah diperbaiki',
                ComplaintStatus::Ditolak => 'Belum diperbaiki · laporan ditolak',
                default => 'Belum diperbaiki',
            },
            'duration_label' => match ($complaint->status) {
                ComplaintStatus::Selesai => "Selesai setelah {$elapsedDays} hari sejak laporan masuk",
                ComplaintStatus::Ditolak => "Laporan ditolak setelah {$elapsedDays} hari",
                default => "Laporan sudah masuk {$elapsedDays} hari lalu",
            },
            'updated_at' => $statusDate,
        ];
    }
}
