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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Western (tropical) zodiac sign.
 *
 * Signs start and end on fixed calendar days, both inclusive.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
enum WesternZodiacSign: string implements ZodiacSignInterface
{
    case Aries = 'aries';
    case Taurus = 'taurus';
    case Gemini = 'gemini';
    case Cancer = 'cancer';
    case Leo = 'leo';
    case Virgo = 'virgo';
    case Libra = 'libra';
    case Scorpio = 'scorpio';
    case Sagittarius = 'sagittarius';
    case Capricorn = 'capricorn';
    case Aquarius = 'aquarius';
    case Pisces = 'pisces';

    /**
     * The first and last days of each sign, as month and day: "03-21" is March 21st.
     */
    private const array DAYS = [
        'aries' => ['03-21', '04-20'],
        'taurus' => ['04-21', '05-21'],
        'gemini' => ['05-22', '06-21'],
        'cancer' => ['06-22', '07-22'],
        'leo' => ['07-23', '08-23'],
        'virgo' => ['08-24', '09-23'],
        'libra' => ['09-24', '10-23'],
        'scorpio' => ['10-24', '11-22'],
        'sagittarius' => ['11-23', '12-21'],
        'capricorn' => ['12-22', '01-20'],
        'aquarius' => ['01-21', '02-19'],
        'pisces' => ['02-20', '03-20'],
    ];

    /**
     * Returns the sign of the given date.
     *
     * A DateTimeInterface is read in its own timezone, so the whole last day of a
     * sign belongs to it, whatever the time. An int, a float, or a numeric string
     * is a Unix timestamp, read in the default timezone. Any other string is
     * parsed by DateTimeImmutable.
     *
     * @throws InvalidArgumentException When no date can be read from the value
     */
    public static function fromDate(\DateTimeInterface|\Stringable|string|int|float $date): self
    {
        $monthDay = DateConverter::toDateTime($date)->format('m-d');

        foreach (self::DAYS as $sign => [$start, $end]) {
            if ($start <= $monthDay && $monthDay <= $end) {
                return self::from($sign);
            }
        }

        // Capricorn is the only sign spanning two years, from December 22 to January 20
        return self::Capricorn;
    }

    /**
     * Returns the emoji of the sign.
     */
    public function getEmoji(): string
    {
        return match ($this) {
            self::Aries => '♈',
            self::Taurus => '♉',
            self::Gemini => '♊',
            self::Cancer => '♋',
            self::Leo => '♌',
            self::Virgo => '♍',
            self::Libra => '♎',
            self::Scorpio => '♏',
            self::Sagittarius => '♐',
            self::Capricorn => '♑',
            self::Aquarius => '♒',
            self::Pisces => '♓',
        };
    }

    /**
     * Returns the first day of the sign in the current year, at midnight in the default timezone.
     */
    public function getStartDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable(date('Y').'-'.self::DAYS[$this->value][0]);
    }

    /**
     * Returns the last day of the sign in the current year, at midnight in the default timezone.
     *
     * Capricorn starts in December and ends in January of the next year.
     */
    public function getEndDate(): \DateTimeImmutable
    {
        $year = (int) date('Y') + (self::Capricorn === $this ? 1 : 0);

        return new \DateTimeImmutable($year.'-'.self::DAYS[$this->value][1]);
    }

    /**
     * Returns the name of the sign, in the given locale or in the one of the translator.
     */
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('western.'.$this->value, [], 'SebJeanZodiacBundle', $locale);
    }
}
