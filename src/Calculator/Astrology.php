<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Calculator;

/**
 * Astrology used to compute a zodiac sign.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
enum Astrology: string
{
    case Western = 'western';
    case Chinese = 'chinese';
}
