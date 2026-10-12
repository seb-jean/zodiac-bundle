<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests\Sign;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SebJean\ZodiacBundle\Exception\InvalidArgumentException;
use SebJean\ZodiacBundle\Sign\ChineseZodiacSign;
use SebJean\ZodiacBundle\Sign\ZodiacSignInterface;
use SebJean\ZodiacBundle\Tests\Fixtures\NewMoon;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

final class ChineseZodiacSignTest extends TestCase
{
    #[DataProvider('provideNewYearDates')]
    public function testFromDateAroundTheNewYear(string $date, ChineseZodiacSign $expected): void
    {
        $this->assertSame($expected, ChineseZodiacSign::fromDate(new \DateTimeImmutable($date)));
    }

    /**
     * @return iterable<string, array{string, ChineseZodiacSign}>
     */
    public static function provideNewYearDates(): iterable
    {
        yield 'day before the 2024 New Year' => ['2024-02-09', ChineseZodiacSign::Rabbit];
        yield 'end of the day before the 2024 New Year' => ['2024-02-09 23:59:59', ChineseZodiacSign::Rabbit];
        yield '2024 New Year' => ['2024-02-10', ChineseZodiacSign::Dragon];
        yield 'day before the 2025 New Year' => ['2025-01-28', ChineseZodiacSign::Dragon];
        yield '2025 New Year' => ['2025-01-29', ChineseZodiacSign::Snake];
        yield 'day before the 2026 New Year' => ['2026-02-16', ChineseZodiacSign::Snake];
        yield '2026 New Year' => ['2026-02-17', ChineseZodiacSign::Horse];
    }

    #[DataProvider('provideSampleDates')]
    public function testFromDateOnSampleDates(string $date, ChineseZodiacSign $expected): void
    {
        $this->assertSame($expected, ChineseZodiacSign::fromDate(new \DateTimeImmutable($date)));
    }

    /**
     * @return iterable<array{string, ChineseZodiacSign}>
     */
    public static function provideSampleDates(): iterable
    {
        yield ['1946-04-10', ChineseZodiacSign::Dog];
        yield ['2001-01-24', ChineseZodiacSign::Snake];
        yield ['2002-02-12', ChineseZodiacSign::Horse];
        yield ['2003-02-01', ChineseZodiacSign::Goat];
        yield ['2004-01-22', ChineseZodiacSign::Monkey];
        yield ['2005-02-09', ChineseZodiacSign::Rooster];
        yield ['2006-01-29', ChineseZodiacSign::Dog];
        yield ['2007-02-18', ChineseZodiacSign::Pig];
        yield ['2008-02-07', ChineseZodiacSign::Rat];
        yield ['2009-01-26', ChineseZodiacSign::Ox];
        yield ['2010-02-14', ChineseZodiacSign::Tiger];
        yield ['2011-02-03', ChineseZodiacSign::Rabbit];
        yield ['2012-01-23', ChineseZodiacSign::Dragon];
    }

    #[DataProvider('provideSupportedRangeLimits')]
    public function testSupportedRangeLimits(string $date, ChineseZodiacSign $expected): void
    {
        $this->assertSame($expected, ChineseZodiacSign::fromDate(new \DateTimeImmutable($date)));
    }

    /**
     * @return iterable<string, array{string, ChineseZodiacSign}>
     */
    public static function provideSupportedRangeLimits(): iterable
    {
        yield 'first supported day, before the 1900 New Year' => ['1900-01-01', ChineseZodiacSign::Pig];
        yield '1900 New Year' => ['1900-01-31', ChineseZodiacSign::Rat];
        yield 'last supported day' => ['2100-12-31', ChineseZodiacSign::Monkey];
    }

    #[DataProvider('provideUnsupportedDates')]
    public function testFromDateRejectsDatesOutOfTheSupportedRange(string $date): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Chinese zodiac sign can only be computed from 1900 to 2100');

        ChineseZodiacSign::fromDate(new \DateTimeImmutable($date));
    }

    /**
     * @return iterable<array{string}>
     */
    public static function provideUnsupportedDates(): iterable
    {
        yield ['1899-12-31'];
        yield ['2101-01-01'];
    }

    public function testNewMoonCalculationMatchesReferenceValues(): void
    {
        // Meeus, "Astronomical Algorithms", example 49.a
        $this->assertEqualsWithDelta(2443192.65118, NewMoon::julianEphemerisDay(-283), 0.000005);

        // published new moons, to the minute
        $this->assertEqualsWithDelta(self::utc('2024-02-09 22:59'), NewMoon::closestTo(self::utc('2024-02-10')), 60);
        $this->assertEqualsWithDelta(self::utc('2025-01-29 12:36'), NewMoon::closestTo(self::utc('2025-01-29')), 60);
    }

    /**
     * Each Chinese New Year's Day is the day of a new moon in China: UTC+8 since
     * 1929, Beijing mean time before.
     */
    public function testNewYearsAreNewMoonsInChina(): void
    {
        $newYears = new \ReflectionClassConstant(ChineseZodiacSign::class, 'NEW_YEARS')->getValue();

        $this->assertIsArray($newYears);
        $this->assertSame(range(1900, 2100), array_keys($newYears));

        foreach ($newYears as $year => $monthDay) {
            $this->assertIsInt($monthDay);

            $newYear = self::utc(\sprintf('%d-%02d-%02d 12:00', $year, intdiv($monthDay, 100), $monthDay % 100));
            $newMoon = NewMoon::closestTo($newYear) + ($year < 1929 ? 27940 : 28800);

            $this->assertSame(gmdate('Y-m-d', $newYear), gmdate('Y-m-d', (int) floor($newMoon)), \sprintf('New Year %d', $year));
        }
    }

    public function testAcceptsAString(): void
    {
        $this->assertSame(ChineseZodiacSign::Dragon, ChineseZodiacSign::fromDate('2024-02-10'));
    }

    public function testAcceptsATimestamp(): void
    {
        // 2024-06-15 12:00:00 UTC, in the year of the Dragon in every timezone
        $this->assertSame(ChineseZodiacSign::Dragon, ChineseZodiacSign::fromDate(1718452800));
    }

    #[DataProvider('provideEmojis')]
    public function testGetEmoji(ChineseZodiacSign $sign, string $expected): void
    {
        $this->assertSame($expected, $sign->getEmoji());
    }

    /**
     * @return iterable<array{ChineseZodiacSign, string}>
     */
    public static function provideEmojis(): iterable
    {
        yield [ChineseZodiacSign::Rat, '🐀'];
        yield [ChineseZodiacSign::Ox, '🐂'];
        yield [ChineseZodiacSign::Tiger, '🐅'];
        yield [ChineseZodiacSign::Rabbit, '🐇'];
        yield [ChineseZodiacSign::Dragon, '🐉'];
        yield [ChineseZodiacSign::Snake, '🐍'];
        yield [ChineseZodiacSign::Horse, '🐎'];
        yield [ChineseZodiacSign::Goat, '🐐'];
        yield [ChineseZodiacSign::Monkey, '🐒'];
        yield [ChineseZodiacSign::Rooster, '🐓'];
        yield [ChineseZodiacSign::Dog, '🐕'];
        yield [ChineseZodiacSign::Pig, '🐖'];
    }

    public function testValues(): void
    {
        $this->assertSame(
            ['rat', 'ox', 'tiger', 'rabbit', 'dragon', 'snake', 'horse', 'goat', 'monkey', 'rooster', 'dog', 'pig'],
            array_map(static fn (ChineseZodiacSign $sign): string => $sign->value, ChineseZodiacSign::cases()),
        );
    }

    public function testImplementsZodiacSignInterface(): void
    {
        $this->assertInstanceOf(ZodiacSignInterface::class, ChineseZodiacSign::Rat);
    }

    #[DataProvider('provideTranslations')]
    public function testTrans(ChineseZodiacSign $sign, string $locale, string $expected): void
    {
        $this->assertSame($expected, $sign->trans(self::translator(), $locale));
    }

    /**
     * @return iterable<array{ChineseZodiacSign, string, string}>
     */
    public static function provideTranslations(): iterable
    {
        yield [ChineseZodiacSign::Ox, 'fr', 'Bœuf'];
        yield [ChineseZodiacSign::Rat, 'it', 'Topo'];
        yield [ChineseZodiacSign::Pig, 'es', 'Cerdo'];
        yield [ChineseZodiacSign::Goat, 'de', 'Schaf'];
    }

    /**
     * Returns the Unix timestamp of the given date, read in UTC.
     */
    private static function utc(string $date): int
    {
        return new \DateTimeImmutable($date, new \DateTimeZone('UTC'))->getTimestamp();
    }

    private static function translator(): Translator
    {
        $translator = new Translator('en');
        $translator->addLoader('xlf', new XliffFileLoader());

        foreach (['de', 'en', 'es', 'fr', 'it'] as $locale) {
            $translator->addResource('xlf', \dirname(__DIR__, 2).'/translations/SebJeanZodiacBundle.'.$locale.'.xlf', $locale, 'SebJeanZodiacBundle');
        }

        return $translator;
    }
}
