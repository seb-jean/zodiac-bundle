<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Sign;

use SebJean\ZodiacBundle\Exception\InvalidArgumentException;

/**
 * Converts the date representations accepted by the bundle to a date.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * @internal
 */
final class DateConverter
{
    /**
     * Returns the given value as a date.
     *
     * A DateTimeInterface is returned as is, so it is read in its own timezone. An
     * int, a float, or a numeric string is a Unix timestamp, read in the default
     * timezone. Any other string is parsed by DateTimeImmutable.
     *
     * @throws InvalidArgumentException When no date can be read from the value
     */
    public static function toDateTime(\DateTimeInterface|\Stringable|string|int|float $date): \DateTimeInterface
    {
        if ($date instanceof \DateTimeInterface) {
            return $date;
        }

        if (\is_int($date) || \is_float($date)) {
            try {
                $dateTime = \DateTimeImmutable::createFromTimestamp($date);
            } catch (\DateRangeError $e) {
                // var_export() writes NAN without coercing it to string, which PHP 8.5 warns about
                throw new InvalidArgumentException(\sprintf('Cannot read a date from the timestamp "%s".', var_export($date, true)), previous: $e);
            }

            // createFromTimestamp() returns a date in UTC
            return $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }

        $date = (string) $date;

        if (is_numeric($date)) {
            return self::toDateTime(0 + $date);
        }

        if ('' === trim($date)) {
            throw new InvalidArgumentException('Cannot read a date from an empty string.');
        }

        try {
            $dateTime = new \DateTimeImmutable($date);
        } catch (\DateMalformedStringException $e) {
            throw new InvalidArgumentException(\sprintf('Cannot read a date from "%s".', $date), previous: $e);
        }

        // PHP moves an impossible date like "1990-04-31" to the next month, only reporting a warning
        $errors = \DateTimeImmutable::getLastErrors();
        if (false !== $errors && $errors['warning_count'] > 0) {
            throw new InvalidArgumentException(\sprintf('Cannot read a date from "%s": %s.', $date, implode(', ', $errors['warnings'])));
        }

        return $dateTime;
    }
}
