<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Provides the zodiac sign calculator and its Twig filter and function.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
final class SebJeanZodiacBundle extends AbstractBundle
{
    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->import('../config/services.php');

        // Twig is optional
        $bundles = $container->getParameter('kernel.bundles');
        if (\is_array($bundles) && isset($bundles['TwigBundle'])) {
            $configurator->import('../config/twig.php');
        }
    }
}
