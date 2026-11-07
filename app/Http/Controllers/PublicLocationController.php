<?php

namespace App\Http\Controllers;

use App\Services\ReverseGeocodeLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicLocationController extends Controller
{
    public function reverse(Request $request, ReverseGeocodeLocation $reverseGeocodeLocation): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'latitude.required' => 'Lokasi latitude belum tersedia.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude berada di luar rentang yang valid.',
            'longitude.required' => 'Lokasi longitude belum tersedia.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude berada di luar rentang yang valid.',
        ]);

        $address = $reverseGeocodeLocation->lookup((float) $validated['latitude'], (float) $validated['longitude']);

        if ($address === null) {
            return response()->json([
                'message' => 'Alamat otomatis belum dapat ditemukan. Koordinat sudah tersimpan di formulir; silakan isi nama jalan, kelurahan, dan kecamatan secara manual.',
            ], 503);
        }

        return response()->json($address);
    }
}
