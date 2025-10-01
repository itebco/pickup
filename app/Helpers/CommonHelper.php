<?php

namespace App\Helpers;

use App\Models\Package;
use Illuminate\Support\Facades\DB;

class CommonHelper
{
    /**
     * Generate a unique package code in the format YYMMDD_001
     * where YYMMDD is the current date and 001 is an incrementing number
     *
     * @return string
     */
    public static function generatePackageCode()
    {
        $dateCode = now()->format('ymd');

        // Get the last package code for today
        $lastPackage = Package::where('package_code', 'LIKE', $dateCode . '%')
            ->withTrashed() // Include trashed packages to avoid conflicts with previously deleted codes
            ->orderBy(DB::raw('CAST(SUBSTRING(package_code, 8) AS UNSIGNED)'), 'DESC')
            ->first();

        if ($lastPackage) {
            // Extract the number part and increment
            $lastNumber = (int)substr($lastPackage->package_code, 7); // Get number after underscore
            $newNumber = $lastNumber + 1;
        } else {
            // First package for this date
            $newNumber = 1;
        }

        // Format with leading zeros (3 digits)
        $formattedNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return $dateCode . '_' . $formattedNumber;
    }
}
