<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class PublicMapController extends Controller
{
    public function index(): View
    {
        return view('public.map.index');
    }

    public function data(): JsonResponse
    {
        $cutoff = now()->subDays(15);
        $complaints = Complaint::query()
            ->select([
                'id', 'complaint_code', 'category_id', 'title', 'description', 'address',
                'district', 'village', 'latitude', 'longitude', 'status', 'created_at', 'updated_at',
            ])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($query) use ($cutoff): void {
                $query->where('status', '!=', ComplaintStatus::Selesai->value)
                    ->orWhere(function ($resolvedQuery) use ($cutoff): void {
                        $resolvedQuery->where('status', ComplaintStatus::Selesai->value)
                            ->where(function ($historyQuery) use ($cutoff): void {
                                $historyQuery->whereHas('statusHistories', fn ($query) => $query
                                    ->where('new_status', ComplaintStatus::Selesai->value)
                                    ->where('created_at', '>', $cutoff))
                                    ->orWhere(function ($legacyQuery) use ($cutoff): void {
                                        $legacyQuery->whereDoesntHave('statusHistories', fn ($query) => $query
                                            ->where('new_status', ComplaintStatus::Selesai->value))
                                            ->where('updated_at', '>', $cutoff);
                                    });
                            });
                    });
            })
            ->with([
                'category:id,name',
                'statusHistories' => fn ($query) => $query->select('id', 'complaint_id', 'new_status', 'created_at')->latest()->limit(1),
            ])
            ->orderByDesc('created_at')
            ->get()
            ->map(function (Complaint $complaint): array {
                $lastStatusHistory = $complaint->statusHistories->first();
                $resolvedAt = $complaint->status === ComplaintStatus::Selesai
                    ? $lastStatusHistory?->created_at ?? $complaint->updated_at
                    : null;
                $decisionAt = in_array($complaint->status, [ComplaintStatus::Selesai, ComplaintStatus::Ditolak], true)
                    ? $lastStatusHistory?->created_at ?? $complaint->updated_at
                    : now();
                $elapsedDays = (int) $complaint->created_at->diffInDays($decisionAt);

                $elapsedLabel = match ($complaint->status) {
                    ComplaintStatus::Selesai => "Selesai setelah {$elapsedDays} hari sejak laporan masuk",
                    ComplaintStatus::Ditolak => "Ditolak setelah {$elapsedDays} hari",
                    default => "Laporan masuk {$elapsedDays} hari lalu",
                };

                return [
                    'code' => $complaint->complaint_code,
                    'title' => $complaint->title,
                    'category' => $complaint->category->name,
                    'description' => $complaint->description,
                    'address' => $complaint->address,
                    'district' => $complaint->district,
                    'village' => $complaint->village,
                    'latitude' => (float) $complaint->latitude,
                    'longitude' => (float) $complaint->longitude,
                    'status' => $complaint->status->value,
                    'status_label' => $complaint->status->label(),
                    'repair_label' => match ($complaint->status) {
                        ComplaintStatus::Selesai => 'Sudah diperbaiki',
                        ComplaintStatus::Ditolak => 'Belum diperbaiki · laporan ditolak',
                        default => 'Belum diperbaiki',
                    },
                    'elapsed_days' => $elapsedDays,
                    'elapsed_label' => $elapsedLabel,
                    'reported_at' => $complaint->created_at->translatedFormat('d M Y'),
                    'status_updated_at' => ($lastStatusHistory?->created_at ?? $complaint->updated_at)->translatedFormat('d M Y'),
                    'tracking_url' => route('tracking.show', $complaint->complaint_code),
                ];
            })
            ->values();

        return response()->json([
            'reports' => $complaints,
            'count' => $complaints->count(),
        ]);
    }
}
