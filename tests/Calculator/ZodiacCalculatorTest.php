<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests\Calculator;

use PHPUnit\Framework\TestCase;
use SebJean\ZodiacBundle\Calculator\Astrology;
use SebJean\ZodiacBundle\Calculator\ZodiacCalculator;
use SebJean\ZodiacBundle\Exception\InvalidArgumentException;
use SebJean\ZodiacBundle\Sign\ChineseZodiacSign;
use SebJean\ZodiacBundle\Sign\WesternZodiacSign;

final class ZodiacCalculatorTest extends TestCase
{
    public function testCalculatesTheWesternSignByDefault(): void
    {
        $this->assertSame(WesternZodiacSign::Aries, new ZodiacCalculator()->calculate('1977-03-27'));
    }

    public function testCalculatesTheSignOfTheGivenAstrology(): void
    {
        $calculator = new ZodiacCalculator();

        $this->assertSame(WesternZodiacSign::Aries, $calculator->calculate('1977-03-27', Astrology::Western));
        $this->assertSame(ChineseZodiacSign::Snake, $calculator->calculate('1977-03-27', Astrology::Chinese));
    }

    public function testAcceptsTheAstrologyAsAString(): void
    {
        $calculator = new ZodiacCalculator();

        $this->assertSame(WesternZodiacSign::Aries, $calculator->calculate('1977-03-27', 'western'));
        $this->assertSame(ChineseZodiacSign::Snake, $calculator->calculate('1977-03-27', 'chinese'));
    }

    public function testRejectsAnUnknownAstrology(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown astrology "mayan", expected one of "western", "chinese".');

        new ZodiacCalculator()->calculate('1977-03-27', 'mayan');
    }
}
