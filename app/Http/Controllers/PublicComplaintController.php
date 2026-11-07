<?php

namespace App\Http\Controllers;

use App\Enums\ComplaintStatus;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class PublicComplaintController extends Controller
{
    public function create(Request $request): View
    {
        $selectedCategory = Category::query()
            ->where('is_active', true)
            ->whereKey($request->query('category'))
            ->value('id');

        return view('public.complaints.create', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'selectedCategory' => old('category_id', $selectedCategory),
        ]);
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $complaintCode = $this->generateComplaintCode();
        $photoPath = $request->file('photo')->store('complaints', 'public');

        if ($photoPath === false) {
            throw new RuntimeException('Foto pengaduan tidak dapat disimpan.');
        }

        try {
            $complaint = DB::transaction(function () use ($request, $photoPath, $complaintCode): Complaint {
                $complaint = Complaint::query()->create([
                    ...$request->safe()->except(['photo', 'agreement']),
                    'complaint_code' => $complaintCode,
                    'photo_path' => $photoPath,
                    'status' => ComplaintStatus::Pending,
                ]);

                $complaint->statusHistories()->create([
                    'old_status' => null,
                    'new_status' => ComplaintStatus::Pending,
                    'user_id' => null,
                ]);

                return $complaint;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($photoPath);

            throw $exception;
        }

        return redirect()->route('complaints.success', $complaint->complaint_code);
    }

    private function generateComplaintCode(): string
    {
        do {
            $code = 'ADM-'.Str::upper(Str::random(16));
        } while (Complaint::query()->where('complaint_code', $code)->exists());

        return $code;
    }

    public function success(string $code): View
    {
        $complaint = Complaint::query()
            ->where('complaint_code', $code)
            ->firstOrFail();

        return view('public.complaints.success', compact('complaint'));
    }
}
