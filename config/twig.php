<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use SebJean\ZodiacBundle\Twig\ZodiacExtension;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('seb_jean_zodiac.twig_extension', ZodiacExtension::class)
            ->tag('twig.extension')
    ;
};
