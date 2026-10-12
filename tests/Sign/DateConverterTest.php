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
use SebJean\ZodiacBundle\Exception\ExceptionInterface;
use SebJean\ZodiacBundle\Exception\InvalidArgumentException;
use SebJean\ZodiacBundle\Sign\DateConverter;

final class DateConverterTest extends TestCase
{
    private string $defaultTimezone;

    protected function setUp(): void
    {
        // timestamps are read in the default timezone
        $this->defaultTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->defaultTimezone);
    }

    #[DataProvider('provideDates')]
    public function testToDateTime(\DateTimeInterface|\Stringable|string|int|float $date, string $expected): void
    {
        $this->assertSame($expected, DateConverter::toDateTime($date)->format('Y-m-d'));
    }

    /**
     * @return iterable<string, array{\DateTimeInterface|\Stringable|string|int|float, string}>
     */
    public static function provideDates(): iterable
    {
        yield 'DateTimeImmutable' => [new \DateTimeImmutable('1977-03-27'), '1977-03-27'];
        yield 'DateTime' => [new \DateTime('1977-03-27'), '1977-03-27'];
        yield 'DateTime read in its own timezone' => [new \DateTimeImmutable('1977-03-27 00:30', new \DateTimeZone('Asia/Tokyo')), '1977-03-27'];
        yield 'timestamp' => [228268800, '1977-03-27'];
        yield 'timestamp before 1970' => [-86400, '1969-12-31'];
        yield 'float timestamp' => [228268800.9, '1977-03-27'];
        yield 'float timestamp in the last second of 1969' => [-0.5, '1969-12-31'];
        yield 'float timestamp in the last second of a day before 1970' => [-86400.5, '1969-12-30'];
        yield 'numeric string in the last second of 1969' => ['-0.5', '1969-12-31'];
        yield 'numeric string' => ['228268800', '1977-03-27'];
        yield 'numeric string with an exponent' => ['2.282688e8', '1977-03-27'];
        yield 'date string' => ['1977-03-27', '1977-03-27'];
        yield 'leap day string' => ['2024-02-29', '2024-02-29'];
        yield 'relative date string' => ['first day of june 1977', '1977-06-01'];
        yield 'date string read in its own timezone' => ['1977-03-27T00:30:00+09:00', '1977-03-27'];
        yield 'Stringable' => [self::stringable('1977-03-27'), '1977-03-27'];
        yield 'numeric Stringable' => [self::stringable('228268800'), '1977-03-27'];
    }

    public function testDateTimeIsReturnedAsIs(): void
    {
        $date = new \DateTime('1977-03-27', new \DateTimeZone('Asia/Tokyo'));

        $this->assertSame($date, DateConverter::toDateTime($date));
    }

    public function testTimestampIsReadInTheDefaultTimezone(): void
    {
        date_default_timezone_set('Asia/Tokyo');

        // 1977-03-26 15:00:00 UTC, already March 27th in Tokyo
        $date = DateConverter::toDateTime(228236400);

        $this->assertSame('1977-03-27', $date->format('Y-m-d'));
        $this->assertSame('Asia/Tokyo', $date->getTimezone()->getName());
    }

    #[DataProvider('provideInvalidDates')]
    public function testToDateTimeRejectsInvalidDates(\Stringable|string|float $date): void
    {
        $this->expectException(InvalidArgumentException::class);

        DateConverter::toDateTime($date);
    }

    /**
     * @return iterable<string, array{\Stringable|string|float}>
     */
    public static function provideInvalidDates(): iterable
    {
        yield 'empty string' => [''];
        yield 'blank string' => ['   '];
        yield 'unreadable string' => ['foobar'];
        yield 'impossible date' => ['1990-04-31'];
        yield 'impossible February date' => ['1990-02-30'];
        yield 'leap day of a common year' => ['2023-02-29'];
        yield 'emoji' => ['🐇'];
        yield 'empty Stringable' => [self::stringable('')];
        yield 'NAN' => [\NAN];
        yield 'INF' => [\INF];
        yield 'float out of the integer range' => [1e30];
        yield 'numeric string out of the integer range' => ['1e30'];
    }

    #[DataProvider('provideInvalidTimestamps')]
    public function testInvalidTimestampMessage(float $timestamp, string $expected): void
    {
        $this->expectExceptionMessage($expected);

        DateConverter::toDateTime($timestamp);
    }

    /**
     * @return iterable<string, array{float, string}>
     */
    public static function provideInvalidTimestamps(): iterable
    {
        yield 'NAN' => [\NAN, 'Cannot read a date from the timestamp "NAN".'];
        yield 'INF' => [\INF, 'Cannot read a date from the timestamp "INF".'];
        yield 'out of the integer range' => [1e30, 'Cannot read a date from the timestamp "1.0E+30".'];
    }

    public function testImpossibleDateMessage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot read a date from "1990-04-31": The parsed date was invalid.');

        DateConverter::toDateTime('1990-04-31');
    }

    public function testExceptionKeepsTheParsingError(): void
    {
        try {
            DateConverter::toDateTime('foobar');
        } catch (InvalidArgumentException $e) {
            $this->assertInstanceOf(ExceptionInterface::class, $e);
            $this->assertSame('Cannot read a date from "foobar".', $e->getMessage());
            $this->assertInstanceOf(\DateMalformedStringException::class, $e->getPrevious());

            return;
        }

        $this->fail('An exception should have been thrown.');
    }

    private static function stringable(string $value): \Stringable
    {
        return new class($value) implements \Stringable {
            public function __construct(private readonly string $value)
            {
            }

            public function __toString(): string
            {
                return $this->value;
            }
        };
    }
}
