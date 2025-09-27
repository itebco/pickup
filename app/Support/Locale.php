<?php

namespace App\Support;

class Locale
{
    public const AVAILABLE_LOCALES = ['en', 'vn', 'ja'];

    public static function flagUrl(string $locale): ?string
    {
        return match ($locale) {
            'en' => url('/flags/GB.png'),
            'vn' => url('/flags/VN.png'),
            'ja' => url('/flags/JP.png'),
            default => null,
        };
    }

    public static function validateLocale(string $locale): bool
    {
        return in_array($locale, self::AVAILABLE_LOCALES);
    }
}
