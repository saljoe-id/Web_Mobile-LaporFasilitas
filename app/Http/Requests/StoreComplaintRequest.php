<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', Rule::exists('categories', 'id')->where('is_active', true)],
            'reporter_name' => ['required', 'string', 'max:100'],
            'reporter_phone' => ['required', 'regex:/^08[0-9]{8,11}$/'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'address' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:100'],
            'village' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'agreement' => ['accepted'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Pilih jenis fasilitas yang dilaporkan.',
            'category_id.exists' => 'Jenis fasilitas tidak tersedia. Silakan pilih kategori yang aktif.',
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'reporter_name.max' => 'Nama pelapor maksimal 100 karakter.',
            'reporter_phone.required' => 'Nomor HP wajib diisi.',
            'reporter_phone.regex' => 'Nomor HP harus diawali 08 dan terdiri dari 10–13 angka.',
            'title.required' => 'Judul pengaduan wajib diisi.',
            'title.max' => 'Judul pengaduan maksimal 150 karakter.',
            'description.required' => 'Deskripsi kerusakan wajib diisi.',
            'description.min' => 'Ceritakan kondisi kerusakan minimal 20 karakter.',
            'description.max' => 'Deskripsi maksimal 5.000 karakter.',
            'address.required' => 'Alamat atau patokan lokasi wajib diisi.',
            'address.max' => 'Alamat maksimal 255 karakter.',
            'district.required' => 'Kecamatan wajib diisi.',
            'district.max' => 'Nama kecamatan maksimal 100 karakter.',
            'village.required' => 'Kelurahan wajib diisi.',
            'village.max' => 'Nama kelurahan maksimal 100 karakter.',
            'latitude.required_with' => 'Isi latitude dan longitude secara berpasangan.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus berada antara -90 dan 90.',
            'longitude.required_with' => 'Isi latitude dan longitude secara berpasangan.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus berada antara -180 dan 180.',
            'photo.required' => 'Foto bukti kerusakan wajib diunggah.',
            'photo.image' => 'File bukti harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat JPG atau PNG.',
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
            'agreement.accepted' => 'Centang persetujuan sebelum mengirim laporan.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'category_id' => 'jenis fasilitas',
            'reporter_name' => 'nama pelapor',
            'reporter_phone' => 'nomor HP',
            'title' => 'judul pengaduan',
            'description' => 'deskripsi kerusakan',
            'address' => 'alamat atau patokan',
            'district' => 'kecamatan',
            'village' => 'kelurahan',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'photo' => 'foto bukti',
            'agreement' => 'persetujuan',
        ];
    }
}
