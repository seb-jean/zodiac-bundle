<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests;

use SebJean\ZodiacBundle\Calculator\ZodiacCalculator;
use SebJean\ZodiacBundle\Sign\ChineseZodiacSign;
use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
use SebJean\ZodiacBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class SebJeanZodiacBundleTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testCalculatorServiceIsPrivate(): void
    {
        $kernel = self::bootKernel();

        $this->assertInstanceOf(ZodiacCalculator::class, static::getContainer()->get('seb_jean_zodiac.calculator'));
        $this->assertFalse($kernel->getContainer()->has('seb_jean_zodiac.calculator'));
    }

    public function testCalculatorCanBeAutowired(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->assertSame($container->get('seb_jean_zodiac.calculator'), $container->get(ZodiacCalculator::class));
    }

    public function testTranslationsAreLoadedByTheFramework(): void
    {
        self::bootKernel();
        $translator = static::getContainer()->get(TranslatorInterface::class);

        $this->assertInstanceOf(TranslatorInterface::class, $translator);
        $this->assertSame('Ratte', ChineseZodiacSign::Rat->trans($translator, 'de'));
        $this->assertSame('Bélier', WesternZodiacSign::Aries->trans($translator, 'fr'));
    }

    public function testTwigFilterAndFunction(): void
    {
        self::bootKernel();
        $twig = static::getContainer()->get('twig');
        $this->assertInstanceOf(Environment::class, $twig);

        $template = $twig->createTemplate("{{ zodiac_sign('1977-03-27')|trans(locale='fr') }} {{ ('1977-03-27'|zodiac_sign('chinese')).emoji }}");

        $this->assertSame('Bélier 🐍', $template->render());
    }

    public function testTwigIsOptional(): void
    {
        // TestKernel does not register TwigBundle in this environment
        $kernel = self::bootKernel(['environment' => 'without_twig']);

        $this->assertFalse($kernel->getContainer()->has('twig'));
        $this->assertFalse(static::getContainer()->has('seb_jean_zodiac.twig_extension'));
        $this->assertInstanceOf(ZodiacCalculator::class, static::getContainer()->get(ZodiacCalculator::class));
    }
}
