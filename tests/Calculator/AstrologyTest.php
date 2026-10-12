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

final class AstrologyTest extends TestCase
{
    public function testValues(): void
    {
        $this->assertSame(
            ['western', 'chinese'],
            array_map(static fn (Astrology $astrology): string => $astrology->value, Astrology::cases()),
        );
    }
}
