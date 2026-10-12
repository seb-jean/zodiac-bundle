<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests\Fixtures;

use SebJean\ZodiacBundle\Calculator\ZodiacCalculator;
use SebJean\ZodiacBundle\SebJeanZodiacBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();

        // the "without_twig" environment checks that Twig is optional
        if ('without_twig' !== $this->environment) {
            yield new TwigBundle();
        }

        yield new SebJeanZodiacBundle();
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2).'/var/test';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(static function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'secret' => 'test',
                'test' => true,
                'translator' => ['fallbacks' => ['en']],
                'form' => true,
            ]);

            // keeps the private services used by the tests, otherwise removed as unused
            $container->setAlias('test.seb_jean_zodiac.calculator', ZodiacCalculator::class)->setPublic(true);
            $container->setAlias('test.form.factory', 'form.factory')->setPublic(true);
        });
    }
}
