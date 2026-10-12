<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests\Twig;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SebJean\ZodiacBundle\Calculator\ZodiacCalculator;
use SebJean\ZodiacBundle\Exception\InvalidArgumentException;
use SebJean\ZodiacBundle\Twig\ZodiacExtension;
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Loader\ArrayLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

final class ZodiacExtensionTest extends TestCase
{
    #[DataProvider('provideTemplates')]
    public function testRender(string $template, string $expected): void
    {
        $this->assertSame($expected, self::render($template, ['date' => new \DateTimeImmutable('1977-03-27')]));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideTemplates(): iterable
    {
        yield 'filter' => ["{{ ('1977-03-27'|zodiac_sign).emoji }}", '♈'];
        yield 'filter with an astrology' => ["{{ ('1977-03-27'|zodiac_sign('chinese')).emoji }}", '🐍'];
        yield 'function' => ["{{ zodiac_sign('1977-03-27').emoji }}", '♈'];
        yield 'function with an astrology' => ["{{ zodiac_sign('1977-03-27', 'chinese').emoji }}", '🐍'];
        yield 'DateTime object' => ['{{ (date|zodiac_sign).emoji }}', '♈'];
        yield 'returns the enum' => ["{{ zodiac_sign('1977-03-27').value }}", 'aries'];
    }

    public function testRejectsAnUnknownAstrology(): void
    {
        try {
            self::render("{{ zodiac_sign('1977-03-27', 'mayan').emoji }}");
        } catch (RuntimeError $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e->getPrevious());

            return;
        }

        $this->fail('An exception should have been thrown.');
    }

    /**
     * @param array<string, mixed> $context
     */
    private static function render(string $template, array $context = []): string
    {
        $twig = new Environment(new ArrayLoader(['template' => $template]));
        $twig->addExtension(new ZodiacExtension());
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([
            ZodiacCalculator::class => static fn (): ZodiacCalculator => new ZodiacCalculator(),
        ]));

        return $twig->render('template', $context);
    }
}
