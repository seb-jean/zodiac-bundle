<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests\Fixtures;

/**
 * Computes the time of new moons, to check the Chinese New Year dates.
 *
 * The algorithm comes from Jean Meeus, "Astronomical Algorithms" (2nd edition),
 * chapter 49. Its error is a few seconds, while checking a New Year's Day only
 * requires the right day.
 */
final class NewMoon
{
    /**
     * Returns the Unix timestamp (UT) of the new moon closest to the given timestamp.
     */
    public static function closestTo(int $timestamp): float
    {
        $lunation = (int) round(($timestamp / 86400 + 2440587.5 - 2451550.09766) / 29.530588861);
        $closest = null;

        foreach ([$lunation - 1, $lunation, $lunation + 1] as $k) {
            $newMoon = self::timestamp($k);

            if (null === $closest || abs($newMoon - $timestamp) < abs($closest - $timestamp)) {
                $closest = $newMoon;
            }
        }

        return $closest;
    }

    /**
     * Returns the Julian Ephemeris Day of the new moon of the given lunation, 0
     * being the new moon of January 6th, 2000.
     */
    public static function julianEphemerisDay(int $k): float
    {
        $t = $k / 1236.85;
        $jde = 2451550.09766 + 29.530588861 * $k + 0.00015437 * $t ** 2 - 0.000000150 * $t ** 3 + 0.00000000073 * $t ** 4;

        $e = 1 - 0.002516 * $t - 0.0000074 * $t ** 2;
        $m = self::radians(2.5534 + 29.10535670 * $k - 0.0000014 * $t ** 2 - 0.00000011 * $t ** 3);
        $mp = self::radians(201.5643 + 385.81693528 * $k + 0.0107582 * $t ** 2 + 0.00001238 * $t ** 3 - 0.000000058 * $t ** 4);
        $f = self::radians(160.7108 + 390.67050284 * $k - 0.0016118 * $t ** 2 - 0.00000227 * $t ** 3 + 0.000000011 * $t ** 4);
        $omega = self::radians(124.7746 - 1.56375588 * $k + 0.0020672 * $t ** 2 + 0.00000215 * $t ** 3);

        $jde += -0.40720 * sin($mp) + 0.17241 * $e * sin($m) + 0.01608 * sin(2 * $mp) + 0.01039 * sin(2 * $f)
            + 0.00739 * $e * sin($mp - $m) - 0.00514 * $e * sin($mp + $m) + 0.00208 * $e * $e * sin(2 * $m)
            - 0.00111 * sin($mp - 2 * $f) - 0.00057 * sin($mp + 2 * $f) + 0.00056 * $e * sin(2 * $mp + $m)
            - 0.00042 * sin(3 * $mp) + 0.00042 * $e * sin($m + 2 * $f) + 0.00038 * $e * sin($m - 2 * $f)
            - 0.00024 * $e * sin(2 * $mp - $m) - 0.00017 * sin($omega) - 0.00007 * sin($mp + 2 * $m)
            + 0.00004 * sin(2 * $mp - 2 * $f) + 0.00004 * sin(3 * $m) + 0.00003 * sin($mp + $m - 2 * $f)
            + 0.00003 * sin(2 * $mp + 2 * $f) - 0.00003 * sin($mp + $m + 2 * $f) + 0.00003 * sin($mp - $m + 2 * $f)
            - 0.00002 * sin($mp - $m - 2 * $f) - 0.00002 * sin(3 * $mp + $m) + 0.00002 * sin(4 * $mp);

        // planetary arguments
        foreach ([
            [0.000325, 299.77 + 0.107408 * $k - 0.009173 * $t ** 2],
            [0.000165, 251.88 + 0.016321 * $k],
            [0.000164, 251.83 + 26.651886 * $k],
            [0.000126, 349.42 + 36.412478 * $k],
            [0.000110, 84.66 + 18.206239 * $k],
            [0.000062, 141.74 + 53.303771 * $k],
            [0.000060, 207.14 + 2.453732 * $k],
            [0.000056, 154.84 + 7.306860 * $k],
            [0.000047, 34.52 + 27.261239 * $k],
            [0.000042, 207.19 + 0.121824 * $k],
            [0.000040, 291.34 + 1.844379 * $k],
            [0.000037, 161.72 + 24.198154 * $k],
            [0.000035, 239.56 + 25.513099 * $k],
            [0.000023, 331.55 + 3.592518 * $k],
        ] as [$coefficient, $angle]) {
            $jde += $coefficient * sin(self::radians($angle));
        }

        return $jde;
    }

    /**
     * Returns the Unix timestamp (UT) of the new moon of the given lunation.
     */
    private static function timestamp(int $k): float
    {
        $jde = self::julianEphemerisDay($k);

        return ($jde - 2440587.5) * 86400 - self::deltaT(2000 + ($jde - 2451545) / 365.25);
    }

    /**
     * Returns the difference between Terrestrial Time and Universal Time, in
     * seconds, from the polynomials of Espenak and Meeus (NASA).
     */
    private static function deltaT(float $year): float
    {
        return match (true) {
            $year < 1920 => -2.79 + 1.494119 * ($t = $year - 1900) - 0.0598939 * $t ** 2 + 0.0061966 * $t ** 3 - 0.000197 * $t ** 4,
            $year < 1941 => 21.20 + 0.84493 * ($t = $year - 1920) - 0.076100 * $t ** 2 + 0.0020936 * $t ** 3,
            $year < 1961 => 29.07 + 0.407 * ($t = $year - 1950) - $t ** 2 / 233 + $t ** 3 / 2547,
            $year < 1986 => 45.45 + 1.067 * ($t = $year - 1975) - $t ** 2 / 260 - $t ** 3 / 718,
            $year < 2005 => 63.86 + 0.3345 * ($t = $year - 2000) - 0.060374 * $t ** 2 + 0.0017275 * $t ** 3 + 0.000651814 * $t ** 4 + 0.00002373599 * $t ** 5,
            $year < 2050 => 62.92 + 0.32217 * ($t = $year - 2000) + 0.005589 * $t ** 2,
            default => -20 + 32 * (($year - 1820) / 100) ** 2 - 0.5628 * (2150 - $year),
        };
    }

    private static function radians(float $degrees): float
    {
        return deg2rad(fmod($degrees, 360));
    }
}
