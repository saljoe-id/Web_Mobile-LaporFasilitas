<?php

namespace Tests\Feature;

use App\Enums\ComplaintStatus;
use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicComplaintWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_residents_can_open_the_submission_and_tracking_screens(): void
    {
        Category::factory()->create(['name' => 'Jalan & Jembatan']);

        $this->get(route('complaints.create'))
            ->assertOk()
            ->assertSee('Buat pengaduan')
            ->assertSee('Jalan & Jembatan');

        $this->get(route('tracking.index'))
            ->assertOk()
            ->assertSee('Lacak pengaduan');
    }

    public function test_valid_submission_saves_pending_report_photo_and_first_history(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();

        $response = $this->post(route('complaints.store'), $this->validPayload($category->id));
        $response->assertSessionHasNoErrors();

        $complaint = Complaint::query()->firstOrFail();
        $response->assertRedirect(route('complaints.success', $complaint->complaint_code));
        $this->get(route('complaints.success', $complaint->complaint_code))
            ->assertOk()
            ->assertSee($complaint->complaint_code);
        $this->assertMatchesRegularExpression('/^ADM-[A-Z0-9]{16}$/', $complaint->complaint_code);
        $this->assertSame(ComplaintStatus::Pending, $complaint->status);
        $this->assertDatabaseHas('complaint_status_histories', [
            'complaint_id' => $complaint->id,
            'new_status' => ComplaintStatus::Pending->value,
            'user_id' => null,
        ]);
        Storage::disk('public')->assertExists($complaint->photo_path);
    }

    public function test_submission_without_required_fields_returns_clear_validation_errors(): void
    {
        $this->from(route('complaints.create'))
            ->post(route('complaints.store'), [])
            ->assertRedirect(route('complaints.create'))
            ->assertSessionHasErrors([
                'category_id',
                'reporter_name',
                'reporter_phone',
                'title',
                'description',
                'address',
                'district',
                'village',
                'photo',
                'agreement',
            ])
            ->assertSessionHasErrors([
                'reporter_name' => 'Nama pelapor wajib diisi.',
                'photo' => 'Foto bukti kerusakan wajib diunggah.',
            ]);

        $this->assertDatabaseCount('complaints', 0);
    }

    public function test_invalid_photo_is_rejected_without_saving_a_report(): void
    {
        $category = Category::factory()->create();
        $payload = $this->validPayload($category->id);
        $payload['photo'] = UploadedFile::fake()->create('bukan-gambar.txt', 20, 'text/plain');

        $this->from(route('complaints.create'))
            ->post(route('complaints.store'), $payload)
            ->assertRedirect(route('complaints.create'))
            ->assertSessionHasErrors(['photo']);

        $this->assertDatabaseCount('complaints', 0);
        $this->assertDatabaseCount('complaint_status_histories', 0);
    }

    public function test_invalid_coordinates_are_rejected(): void
    {
        $category = Category::factory()->create();
        $payload = $this->validPayload($category->id);
        $payload['latitude'] = '91';
        $payload['longitude'] = '181';

        $this->from(route('complaints.create'))
            ->post(route('complaints.store'), $payload)
            ->assertRedirect(route('complaints.create'))
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_tracking_finds_a_report_by_complaint_code_without_exposing_personal_data(): void
    {
        $complaint = Complaint::factory()->create([
            'title' => 'Jalan berlubang di dekat taman',
            'reporter_name' => 'Nama Privat',
            'reporter_phone' => '081234567890',
        ]);

        $this->get(route('tracking.index', ['code' => $complaint->complaint_code]))
            ->assertOk()
            ->assertSee($complaint->complaint_code)
            ->assertSee('Jalan berlubang di dekat taman')
            ->assertDontSee('Nama Privat')
            ->assertDontSee('081234567890');
    }

    public function test_tracking_shows_a_clear_message_when_code_is_not_found(): void
    {
        $this->get(route('tracking.index', ['code' => 'ADM-'.str_repeat('Z', 16)]))
            ->assertOk()
            ->assertSee('Pengaduan belum ditemukan');
    }

    /** @return array<string, mixed> */
    private function validPayload(int $categoryId): array
    {
        return [
            'category_id' => $categoryId,
            'reporter_name' => 'Sari Wulandari',
            'reporter_phone' => '081234567890',
            'title' => 'Lampu jalan tidak menyala',
            'description' => 'Lampu jalan di depan taman tidak menyala sejak beberapa malam.',
            'address' => 'Jalan Merdeka dekat taman',
            'district' => 'Coblong',
            'village' => 'Dago',
            'latitude' => '-6.8836000',
            'longitude' => '107.6102000',
            'photo' => UploadedFile::fake()->image('lampu-rusak.jpg'),
            'agreement' => '1',
        ];
    }
}
