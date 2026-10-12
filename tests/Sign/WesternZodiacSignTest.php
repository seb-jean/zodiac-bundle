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
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
use SebJean\ZodiacBundle\Sign\ZodiacSignInterface;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

#[Group('time-sensitive')]
final class WesternZodiacSignTest extends TestCase
{
    #[DataProvider('provideBoundaryDates')]
    public function testFromDateOnBoundaries(string $date, WesternZodiacSign $expected): void
    {
        $this->assertSame($expected, WesternZodiacSign::fromDate(new \DateTimeImmutable($date)));
    }

    /**
     * @return iterable<string, array{string, WesternZodiacSign}>
     */
    public static function provideBoundaryDates(): iterable
    {
        yield 'Capricorn first day' => ['2025-12-22', WesternZodiacSign::Capricorn];
        yield 'Capricorn last day of the year' => ['2025-12-31', WesternZodiacSign::Capricorn];
        yield 'Capricorn first day of the year' => ['2025-01-01', WesternZodiacSign::Capricorn];
        yield 'Capricorn last day' => ['2025-01-20', WesternZodiacSign::Capricorn];
        yield 'Aquarius first day' => ['2025-01-21', WesternZodiacSign::Aquarius];
        yield 'Aquarius last day' => ['2025-02-19', WesternZodiacSign::Aquarius];
        yield 'Pisces first day' => ['2025-02-20', WesternZodiacSign::Pisces];
        yield 'Pisces last day' => ['2025-03-20', WesternZodiacSign::Pisces];
        yield 'Aries first day' => ['2025-03-21', WesternZodiacSign::Aries];
        yield 'Aries last day' => ['2025-04-20', WesternZodiacSign::Aries];
        yield 'Taurus first day' => ['2025-04-21', WesternZodiacSign::Taurus];
        yield 'Taurus last day' => ['2025-05-21', WesternZodiacSign::Taurus];
        yield 'Gemini first day' => ['2025-05-22', WesternZodiacSign::Gemini];
        yield 'Gemini last day' => ['2025-06-21', WesternZodiacSign::Gemini];
        yield 'Cancer first day' => ['2025-06-22', WesternZodiacSign::Cancer];
        yield 'Cancer last day' => ['2025-07-22', WesternZodiacSign::Cancer];
        yield 'Leo first day' => ['2025-07-23', WesternZodiacSign::Leo];
        yield 'Leo last day' => ['2025-08-23', WesternZodiacSign::Leo];
        yield 'Virgo first day' => ['2025-08-24', WesternZodiacSign::Virgo];
        yield 'Virgo last day' => ['2025-09-23', WesternZodiacSign::Virgo];
        yield 'Libra first day' => ['2025-09-24', WesternZodiacSign::Libra];
        yield 'Libra last day' => ['2025-10-23', WesternZodiacSign::Libra];
        yield 'Scorpio first day' => ['2025-10-24', WesternZodiacSign::Scorpio];
        yield 'Scorpio last day' => ['2025-11-22', WesternZodiacSign::Scorpio];
        yield 'Sagittarius first day' => ['2025-11-23', WesternZodiacSign::Sagittarius];
        yield 'Sagittarius last day' => ['2025-12-21', WesternZodiacSign::Sagittarius];
    }

    #[DataProvider('provideSampleDates')]
    public function testFromDateOnSampleDates(string $date, WesternZodiacSign $expected): void
    {
        $this->assertSame($expected, WesternZodiacSign::fromDate(new \DateTimeImmutable($date)));
    }

    /**
     * @return iterable<array{string, WesternZodiacSign}>
     */
    public static function provideSampleDates(): iterable
    {
        yield ['1977-03-27', WesternZodiacSign::Aries];
        yield ['1977-04-27', WesternZodiacSign::Taurus];
        yield ['1977-05-27', WesternZodiacSign::Gemini];
        yield ['1977-06-27', WesternZodiacSign::Cancer];
        yield ['1977-07-27', WesternZodiacSign::Leo];
        yield ['1977-06-21', WesternZodiacSign::Gemini];
        yield ['1977-08-27', WesternZodiacSign::Virgo];
        yield ['1977-09-27', WesternZodiacSign::Libra];
        yield ['1977-10-27', WesternZodiacSign::Scorpio];
        yield ['1977-11-27', WesternZodiacSign::Sagittarius];
        yield ['1977-12-27', WesternZodiacSign::Capricorn];
        yield ['1977-12-31', WesternZodiacSign::Capricorn];
        yield ['1977-01-01', WesternZodiacSign::Capricorn];
        yield ['1977-01-15', WesternZodiacSign::Capricorn];
        yield ['1977-01-26', WesternZodiacSign::Aquarius];
        yield ['1977-02-27', WesternZodiacSign::Pisces];
    }

    public function testLastDayOfASignIncludesTheWholeDay(): void
    {
        $this->assertSame(WesternZodiacSign::Aries, WesternZodiacSign::fromDate(new \DateTimeImmutable('2025-04-20 23:59:59')));
        $this->assertSame(WesternZodiacSign::Capricorn, WesternZodiacSign::fromDate(new \DateTimeImmutable('2025-01-20 23:59:59')));
    }

    public function testLeapDay(): void
    {
        $this->assertSame(WesternZodiacSign::Pisces, WesternZodiacSign::fromDate(new \DateTimeImmutable('2024-02-29')));
    }

    public function testUsesTheTimezoneOfTheDate(): void
    {
        $defaultTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');

        try {
            // 2025-03-20 15:30 in UTC, but already March 21st in Tokyo
            $date = new \DateTimeImmutable('2025-03-21 00:30', new \DateTimeZone('Asia/Tokyo'));

            $this->assertSame(WesternZodiacSign::Aries, WesternZodiacSign::fromDate($date));
        } finally {
            date_default_timezone_set($defaultTimezone);
        }
    }

    public function testAcceptsMutableDateTime(): void
    {
        $this->assertSame(WesternZodiacSign::Aries, WesternZodiacSign::fromDate(new \DateTime('2025-03-21')));
    }

    public function testAcceptsAString(): void
    {
        $this->assertSame(WesternZodiacSign::Aries, WesternZodiacSign::fromDate('1977-03-27'));
    }

    public function testAcceptsATimestamp(): void
    {
        // 1977-03-27 00:00:00 UTC, still Aries in every timezone
        $this->assertSame(WesternZodiacSign::Aries, WesternZodiacSign::fromDate(228268800));
    }

    public function testYearBeforeTheCommonEra(): void
    {
        $this->assertSame(WesternZodiacSign::Pisces, WesternZodiacSign::fromDate(new \DateTimeImmutable()->setDate(-44, 3, 15)));
    }

    #[DataProvider('provideEmojis')]
    public function testGetEmoji(WesternZodiacSign $sign, string $expected): void
    {
        $this->assertSame($expected, $sign->getEmoji());
    }

    /**
     * @return iterable<array{WesternZodiacSign, string}>
     */
    public static function provideEmojis(): iterable
    {
        yield [WesternZodiacSign::Aries, '♈'];
        yield [WesternZodiacSign::Taurus, '♉'];
        yield [WesternZodiacSign::Gemini, '♊'];
        yield [WesternZodiacSign::Cancer, '♋'];
        yield [WesternZodiacSign::Leo, '♌'];
        yield [WesternZodiacSign::Virgo, '♍'];
        yield [WesternZodiacSign::Libra, '♎'];
        yield [WesternZodiacSign::Scorpio, '♏'];
        yield [WesternZodiacSign::Sagittarius, '♐'];
        yield [WesternZodiacSign::Capricorn, '♑'];
        yield [WesternZodiacSign::Aquarius, '♒'];
        yield [WesternZodiacSign::Pisces, '♓'];
    }

    public function testValues(): void
    {
        $this->assertSame(
            ['aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo', 'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces'],
            array_map(static fn (WesternZodiacSign $sign): string => $sign->value, WesternZodiacSign::cases()),
        );
    }

    public function testImplementsZodiacSignInterface(): void
    {
        $this->assertInstanceOf(ZodiacSignInterface::class, WesternZodiacSign::Aries);
    }

    #[DataProvider('provideStartAndEndDates')]
    public function testStartAndEndDates(WesternZodiacSign $sign, string $start, string $end): void
    {
        ClockMock::withClockMock(strtotime('2025-06-15 12:00:00'));

        $this->assertSame($start.' 00:00:00', $sign->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertSame($end.' 00:00:00', $sign->getEndDate()->format('Y-m-d H:i:s'));
    }

    /**
     * @return iterable<string, array{WesternZodiacSign, string, string}>
     */
    public static function provideStartAndEndDates(): iterable
    {
        yield 'Aries' => [WesternZodiacSign::Aries, '2025-03-21', '2025-04-20'];
        yield 'Taurus' => [WesternZodiacSign::Taurus, '2025-04-21', '2025-05-21'];
        yield 'Gemini' => [WesternZodiacSign::Gemini, '2025-05-22', '2025-06-21'];
        yield 'Cancer' => [WesternZodiacSign::Cancer, '2025-06-22', '2025-07-22'];
        yield 'Leo' => [WesternZodiacSign::Leo, '2025-07-23', '2025-08-23'];
        yield 'Virgo' => [WesternZodiacSign::Virgo, '2025-08-24', '2025-09-23'];
        yield 'Libra' => [WesternZodiacSign::Libra, '2025-09-24', '2025-10-23'];
        yield 'Scorpio' => [WesternZodiacSign::Scorpio, '2025-10-24', '2025-11-22'];
        yield 'Sagittarius' => [WesternZodiacSign::Sagittarius, '2025-11-23', '2025-12-21'];
        yield 'Aquarius' => [WesternZodiacSign::Aquarius, '2025-01-21', '2025-02-19'];
        yield 'Pisces' => [WesternZodiacSign::Pisces, '2025-02-20', '2025-03-20'];
    }

    public function testCapricornEndsInTheNextYear(): void
    {
        ClockMock::withClockMock(strtotime('2025-06-15 12:00:00'));

        $this->assertSame('2025-12-22', WesternZodiacSign::Capricorn->getStartDate()->format('Y-m-d'));
        $this->assertSame('2026-01-20', WesternZodiacSign::Capricorn->getEndDate()->format('Y-m-d'));
    }

    public function testStartAndEndDatesAreInTheDefaultTimezone(): void
    {
        $defaultTimezone = date_default_timezone_get();
        date_default_timezone_set('Asia/Tokyo');

        try {
            $this->assertSame('Asia/Tokyo', WesternZodiacSign::Aries->getStartDate()->getTimezone()->getName());
            $this->assertSame('Asia/Tokyo', WesternZodiacSign::Aries->getEndDate()->getTimezone()->getName());
        } finally {
            date_default_timezone_set($defaultTimezone);
        }
    }

    /**
     * The start and end dates match the days used by fromDate().
     */
    #[DataProvider('provideSigns')]
    public function testStartAndEndDatesMatchFromDate(WesternZodiacSign $sign): void
    {
        $cases = WesternZodiacSign::cases();
        $next = $cases[(array_search($sign, $cases, true) + 1) % \count($cases)];

        $this->assertSame($sign, WesternZodiacSign::fromDate($sign->getStartDate()));
        $this->assertSame($sign, WesternZodiacSign::fromDate($sign->getEndDate()));
        $this->assertSame($next, WesternZodiacSign::fromDate($sign->getEndDate()->modify('+1 day')));
    }

    /**
     * @return iterable<string, array{WesternZodiacSign}>
     */
    public static function provideSigns(): iterable
    {
        foreach (WesternZodiacSign::cases() as $sign) {
            yield $sign->name => [$sign];
        }
    }

    #[DataProvider('provideTranslations')]
    public function testTrans(WesternZodiacSign $sign, string $locale, string $expected): void
    {
        $this->assertSame($expected, $sign->trans(self::translator(), $locale));
    }

    /**
     * @return iterable<array{WesternZodiacSign, string, string}>
     */
    public static function provideTranslations(): iterable
    {
        yield [WesternZodiacSign::Aries, 'en', 'Aries'];
        yield [WesternZodiacSign::Aries, 'fr', 'Bélier'];
        yield [WesternZodiacSign::Sagittarius, 'de', 'Schütze'];
        yield [WesternZodiacSign::Gemini, 'es', 'Géminis'];
        yield [WesternZodiacSign::Libra, 'it', 'Bilancia'];
    }

    public function testTransUsesTheLocaleOfTheTranslatorByDefault(): void
    {
        $translator = self::translator();
        $translator->setLocale('fr');

        $this->assertSame('Gémeaux', WesternZodiacSign::Gemini->trans($translator));
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
