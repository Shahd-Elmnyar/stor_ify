<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class MainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // Check if the resource is null
        if (is_null($this->resource) || $this->resource instanceof \Illuminate\Http\Resources\MissingValue) {
            return [];
        }

        $data = $this->transformData(parent::toArray($request));
        Log::info('Data before localization:', $data);
        $localizedData = $this->localizeData($data);
        Log::info('Data after localization:', $localizedData);
        return $localizedData;
    }


    /**
     * Localize the data based on the user's locale.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function localizeData(array $data): array
    {
        $userLocale = $this->getUserLocale();
        Log::info('User locale: ' . $userLocale);

        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value[$userLocale])) {
                Log::info("Localizing field '{$key}' with locale '{$userLocale}'");
                $data[$key] = $value[$userLocale];
            } elseif (is_string($value) && $this->isJson($value)) {
                $decodedValue = json_decode($value, true);
                if (is_array($decodedValue) && isset($decodedValue[$userLocale])) {
                    Log::info("Localizing JSON field '{$key}' with locale '{$userLocale}'");
                    $data[$key] = $decodedValue[$userLocale];
                }
            }
        }

        return $data;
    }

    /**
     * Get the user's locale from the database.
     *
     * @return string
     */
    protected function getUserLocale(): string
    {
        if (Auth::check()) {
            $locale = Auth::user()->lang ?? config('app.fallback_locale');
            Log::info('User is authenticated. Locale: ' . $locale);
            return $locale;
        }
        $locale = config('app.fallback_locale');
        Log::info('User is not authenticated. Using fallback locale: ' . $locale);
        return $locale;
    }

    /**
     * Check if a string is valid JSON.
     *
     * @param string $string
     * @return bool
     */
    protected function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Transform the resource data.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function transformData(array $data): array
    {
        // Default transformation (can be overridden in child classes)
        return $data;
    }
}
