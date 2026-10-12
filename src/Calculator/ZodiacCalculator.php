<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Calculator;

use SebJean\ZodiacBundle\Exception\InvalidArgumentException;
use SebJean\ZodiacBundle\Sign\ChineseZodiacSign;
use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
use SebJean\ZodiacBundle\Sign\ZodiacSignInterface;

/**
 * Computes the zodiac sign of a date.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
final class ZodiacCalculator
{
    /**
     * Returns the zodiac sign of the given date.
     *
     * A DateTimeInterface is read in its own timezone. An int, a float, or a numeric
     * string is a Unix timestamp, read in the default timezone. Any other string is
     * parsed by DateTimeImmutable.
     *
     * @param Astrology|string $astrology An Astrology case, or its value: "western" or "chinese"
     *
     * @throws InvalidArgumentException When the astrology is unknown, when no date can be read from the value, or when the date is out of the supported range
     */
    public function calculate(\DateTimeInterface|\Stringable|string|int|float $date, Astrology|string $astrology = Astrology::Western): ZodiacSignInterface
    {
        if (\is_string($astrology)) {
            $astrology = Astrology::tryFrom($astrology) ?? throw new InvalidArgumentException(\sprintf('Unknown astrology "%s", expected one of "%s".', $astrology, implode('", "', array_column(Astrology::cases(), 'value'))));
        }

        return match ($astrology) {
            Astrology::Western => WesternZodiacSign::fromDate($date),
            Astrology::Chinese => ChineseZodiacSign::fromDate($date),
        };
    }
}
