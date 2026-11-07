<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use App\Models\ComplaintStatusHistory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        $start = now()->startOfMonth()->subMonths(11);
        $end = now()->endOfMonth();
        $monthExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $reportCounts = Complaint::query()
            ->selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('month')
            ->pluck('total', 'month');
        $resolvedCounts = ComplaintStatusHistory::query()
            ->selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->where('new_status', ComplaintStatus::Selesai->value)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyTrend = collect(range(0, 11))->map(function (int $offset) use ($start, $reportCounts, $resolvedCounts): array {
            $month = $start->copy()->addMonths($offset);
            $key = $month->format('Y-m');
            $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            return [
                'key' => $key,
                'label' => substr($monthNames[$month->month - 1], 0, 3),
                'full_label' => $monthNames[$month->month - 1].' '.$month->year,
                'reported' => (int) ($reportCounts[$key] ?? 0),
                'resolved' => (int) ($resolvedCounts[$key] ?? 0),
            ];
        });

        $maximum = max(1, (int) $monthlyTrend->max(fn (array $month): int => max($month['reported'], $month['resolved'])));
        $monthlyTrend = $monthlyTrend->map(fn (array $month): array => [
            ...$month,
            'reported_height' => $month['reported'] === 0 ? 0 : max(5, (int) round($month['reported'] / $maximum * 100)),
            'resolved_height' => $month['resolved'] === 0 ? 0 : max(5, (int) round($month['resolved'] / $maximum * 100)),
        ]);
        $hasMonthlyData = $monthlyTrend->contains(fn (array $month): bool => $month['reported'] > 0 || $month['resolved'] > 0);

        return view('welcome', [
            'complaintCount' => Complaint::query()->count(),
            'inProgressCount' => Complaint::query()->where('status', ComplaintStatus::Diproses->value)->count(),
            'completedCount' => Complaint::query()->where('status', ComplaintStatus::Selesai->value)->count(),
            'monthlyTrend' => $monthlyTrend,
            'chartMaximum' => $maximum,
            'hasMonthlyData' => $hasMonthlyData,
        ]);
    }
}
