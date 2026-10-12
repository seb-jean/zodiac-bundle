<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Twig;

use SebJean\ZodiacBundle\Calculator\ZodiacCalculator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Provides the zodiac_sign filter and function, which return the zodiac sign of a date.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * @internal
 */
final class ZodiacExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('zodiac_sign', [ZodiacCalculator::class, 'calculate']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('zodiac_sign', [ZodiacCalculator::class, 'calculate']),
        ];
    }
}
