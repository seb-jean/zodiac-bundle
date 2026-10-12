<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Sign;

use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * A zodiac sign, whatever the astrology.
 *
 * Its name is translated through TranslatableInterface, in the
 * "SebJeanZodiacBundle" translation domain.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
interface ZodiacSignInterface extends TranslatableInterface
{
    /**
     * Returns the emoji of the sign.
     */
    public function getEmoji(): string;
}
