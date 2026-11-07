<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class ReverseGeocodeLocation
{
    /**
     * @return array{street: ?string, village: ?string, district: ?string}|null
     */
    public function lookup(float $latitude, float $longitude): ?array
    {
        $cacheKey = 'reverse-geocode:'.hash('sha256', sprintf('%.5f,%.5f', $latitude, $longitude));
        $cachedAddress = Cache::get($cacheKey);

        if (is_array($cachedAddress)) {
            return $cachedAddress;
        }

        $address = RateLimiter::attempt(
            'nominatim-reverse-global',
            1,
            fn (): ?array => $this->requestAddress($latitude, $longitude),
            1,
        );

        if (! is_array($address)) {
            return null;
        }

        Cache::put($cacheKey, $address, now()->addDays(30));

        return $address;
    }

    /**
     * @return array{street: ?string, village: ?string, district: ?string}|null
     */
    private function requestAddress(float $latitude, float $longitude): ?array
    {
        $applicationUrl = config('app.url');

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Accept-Language' => 'id',
                'Referer' => $applicationUrl,
                'User-Agent' => config('app.name').'/1.0 ('.$applicationUrl.')',
            ])
                ->connectTimeout(3)
                ->timeout(6)
                ->get(rtrim(config('services.nominatim.base_url'), '/').'/reverse', [
                    'format' => 'jsonv2',
                    'addressdetails' => 1,
                    'zoom' => 18,
                    'lat' => $latitude,
                    'lon' => $longitude,
                ]);
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $address = $response->json('address');

        if (! is_array($address)) {
            return null;
        }

        return [
            'street' => $this->firstAddressPart($address, ['road', 'pedestrian', 'residential', 'footway', 'path', 'cycleway', 'service']),
            'village' => $this->firstAddressPart($address, ['village', 'suburb', 'neighbourhood', 'quarter', 'hamlet']),
            'district' => $this->firstAddressPart($address, ['city_district', 'district', 'municipality', 'county']),
        ];
    }

    /** @param array<string, mixed> $address */
    private function firstAddressPart(array $address, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $address[$key] ?? null;

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }
}
