<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests\Functional;

use SebJean\ZodiacBundle\Sign\ChineseZodiacSign;
use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
use SebJean\ZodiacBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testSignsAreSerializedAsTheirValue(): void
    {
        self::bootKernel();
        $serializer = static::getContainer()->get('serializer');
        $this->assertInstanceOf(SerializerInterface::class, $serializer);

        $this->assertSame('"aries"', $serializer->serialize(WesternZodiacSign::Aries, 'json'));
        $this->assertSame(ChineseZodiacSign::Rat, $serializer->deserialize('"rat"', ChineseZodiacSign::class, 'json'));
    }
}
