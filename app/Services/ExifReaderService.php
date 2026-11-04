<?php

namespace App\Services;

use Carbon\Carbon;

class ExifReaderService
{
    /**
     * Extract metadata from an image file path on disk.
     *
     * @return array<string, mixed>
     */
    public function extract(string $filePath): array
    {
        $data = [
            'taken_at' => null,
            'camera_make' => null,
            'camera_model' => null,
            'lens_model' => null,
            'exposure_time' => null,
            'aperture' => null,
            'iso' => null,
            'focal_length' => null,
            'gps_latitude' => null,
            'gps_longitude' => null,
        ];

        if (! function_exists('exif_read_data')) {
            return $data;
        }

        try {
            $exif = @exif_read_data($filePath, 'ANY_TAG', true);
            if (! is_array($exif)) {
                return $data;
            }

            $idf0 = $exif['IFD0'] ?? $exif;
            $exifSub = $exif['EXIF'] ?? $exif;
            $gps = $exif['GPS'] ?? [];

            // Camera make and model
            if (! empty($idf0['Make'])) {
                $data['camera_make'] = trim((string) $idf0['Make']);
            }
            if (! empty($idf0['Model'])) {
                $data['camera_model'] = trim((string) $idf0['Model']);
            }

            // Lens model
            if (! empty($exifSub['UndefinedTag:0xA434'])) {
                $data['lens_model'] = trim((string) $exifSub['UndefinedTag:0xA434']);
            } elseif (! empty($exifSub['LensModel'])) {
                $data['lens_model'] = trim((string) $exifSub['LensModel']);
            }

            // Date taken (DateTimeOriginal preferred, then DateTimeDigitized, then DateTime)
            $dateString = $exifSub['DateTimeOriginal'] ?? $exifSub['DateTimeDigitized'] ?? $idf0['DateTime'] ?? null;
            if ($dateString) {
                try {
                    $data['taken_at'] = Carbon::createFromFormat('Y:m:d H:i:s', trim((string) $dateString));
                } catch (\Throwable) {
                    try {
                        $data['taken_at'] = Carbon::parse($dateString);
                    } catch (\Throwable) {
                        $data['taken_at'] = null;
                    }
                }
            }

            // Exposure time
            if (! empty($exifSub['ExposureTime'])) {
                $data['exposure_time'] = $this->formatExposureTime((string) $exifSub['ExposureTime']);
            }

            // Aperture / FNumber
            if (! empty($exifSub['FNumber'])) {
                $data['aperture'] = $this->formatFNumber((string) $exifSub['FNumber']);
            }

            // ISO
            if (! empty($exifSub['ISOSpeedRatings'])) {
                $iso = $exifSub['ISOSpeedRatings'];
                $data['iso'] = is_array($iso) ? (int) reset($iso) : (int) $iso;
            } elseif (! empty($exifSub['PhotographicSensitivity'])) {
                $iso = $exifSub['PhotographicSensitivity'];
                $data['iso'] = is_array($iso) ? (int) reset($iso) : (int) $iso;
            }

            // Focal length
            if (! empty($exifSub['FocalLength'])) {
                $data['focal_length'] = $this->formatFocalLength((string) $exifSub['FocalLength']);
            }

            // GPS
            if (! empty($gps['GPSLatitude']) && ! empty($gps['GPSLatitudeRef'])) {
                $lat = $this->calculateGpsCoordinate($gps['GPSLatitude'], (string) $gps['GPSLatitudeRef']);
                if ($lat !== null) {
                    $data['gps_latitude'] = round($lat, 7);
                }
            }
            if (! empty($gps['GPSLongitude']) && ! empty($gps['GPSLongitudeRef'])) {
                $lng = $this->calculateGpsCoordinate($gps['GPSLongitude'], (string) $gps['GPSLongitudeRef']);
                if ($lng !== null) {
                    $data['gps_longitude'] = round($lng, 7);
                }
            }
        } catch (\Throwable) {
            // Silently degrade gracefully if exif extraction encounters malformed bytes
        }

        return $data;
    }

    /**
     * Format exposure time to standard photography format (e.g., 1/250s or 2s).
     */
    protected function formatExposureTime(string $val): string
    {
        if (str_contains($val, '/')) {
            $parts = explode('/', $val);
            $num = (float) $parts[0];
            $den = (float) ($parts[1] ?? 1);
            if ($den > 0 && $num > 0) {
                if ($num === 1.0) {
                    return '1/'.(int) $den.'s';
                }
                if ($num < $den) {
                    $reducedDen = round($den / $num);

                    return '1/'.(int) $reducedDen.'s';
                }

                return round($num / $den, 1).'s';
            }
        }

        $numeric = (float) $val;
        if ($numeric > 0 && $numeric < 1) {
            return '1/'.(int) round(1 / $numeric).'s';
        }

        return $val.'s';
    }

    /**
     * Format FNumber (e.g. 28/10 -> f/2.8).
     */
    protected function formatFNumber(string $val): string
    {
        if (str_contains($val, '/')) {
            $parts = explode('/', $val);
            $den = (float) ($parts[1] ?? 1);
            if ($den > 0) {
                $val = (float) $parts[0] / $den;
            }
        }

        $num = (float) $val;

        return 'f/'.round($num, 1);
    }

    /**
     * Format focal length (e.g. 50/1 -> 50mm).
     */
    protected function formatFocalLength(string $val): string
    {
        if (str_contains($val, '/')) {
            $parts = explode('/', $val);
            $den = (float) ($parts[1] ?? 1);
            if ($den > 0) {
                $val = (float) $parts[0] / $den;
            }
        }

        return round((float) $val, 1).'mm';
    }

    /**
     * Convert rational EXIF GPS coordinates to decimal degrees.
     *
     * @param  array<int, string|float>|string  $coordinate
     */
    protected function calculateGpsCoordinate(array|string $coordinate, string $hemisphere): ?float
    {
        if (! is_array($coordinate) || count($coordinate) < 3) {
            return null;
        }

        $degrees = $this->evalRational((string) $coordinate[0]);
        $minutes = $this->evalRational((string) $coordinate[1]);
        $seconds = $this->evalRational((string) $coordinate[2]);

        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

        $hemisphere = strtoupper(trim($hemisphere));
        if ($hemisphere === 'S' || $hemisphere === 'W') {
            $decimal = -$decimal;
        }

        return $decimal;
    }

    protected function evalRational(string $rational): float
    {
        if (str_contains($rational, '/')) {
            $parts = explode('/', $rational);
            $den = (float) ($parts[1] ?? 1);
            if ($den != 0) {
                return (float) $parts[0] / $den;
            }
        }

        return (float) $rational;
    }
}
