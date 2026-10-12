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

use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
use SebJean\ZodiacBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RoutingTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testRouteParametersAreResolvedToSigns(): void
    {
        self::bootKernel();
        $argumentResolver = static::getContainer()->get('argument_resolver');
        $this->assertInstanceOf(ArgumentResolverInterface::class, $argumentResolver);

        $controller = static fn (WesternZodiacSign $sign): WesternZodiacSign => $sign;

        $this->assertSame([WesternZodiacSign::Aries], $argumentResolver->getArguments(new Request([], [], ['sign' => 'aries']), $controller));

        $this->expectException(NotFoundHttpException::class);
        $argumentResolver->getArguments(new Request([], [], ['sign' => 'foo']), $controller);
    }
}
