<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Contracts\View\View;

class StaffDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('staff.dashboard', [
            'complaintCount' => Complaint::query()->count(),
            'newCount' => Complaint::query()->whereIn('status', [ComplaintStatus::Pending->value, ComplaintStatus::Baru->value])->count(),
            'inProgressCount' => Complaint::query()->where('status', ComplaintStatus::Diproses->value)->count(),
            'completedCount' => Complaint::query()->where('status', ComplaintStatus::Selesai->value)->count(),
            'recentComplaints' => Complaint::query()->with('category')->latest()->limit(6)->get(),
        ]);
    }
}
