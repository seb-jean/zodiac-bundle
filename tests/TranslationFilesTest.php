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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SebJean\ZodiacBundle\Sign\ChineseZodiacSign;
use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
use Symfony\Component\Translation\Loader\XliffFileLoader;

final class TranslationFilesTest extends TestCase
{
    private const LOCALES = [
        'af', 'ar', 'az', 'be', 'bg', 'bs', 'ca', 'cs', 'cy', 'da', 'de', 'el', 'en', 'es', 'et', 'eu', 'fa', 'fi', 'fr',
        'gl', 'he', 'hr', 'hu', 'hy', 'id', 'it', 'ja', 'lb', 'lt', 'lv', 'mk', 'mn', 'my', 'nb', 'nl', 'nn', 'no', 'pl',
        'pt', 'pt_BR', 'ro', 'ru', 'sk', 'sl', 'sq', 'sr_Cyrl', 'sr_Latn', 'sv', 'th', 'tl', 'tr', 'uk', 'ur', 'uz', 'vi',
        'zh_CN', 'zh_TW',
    ];

    public function testTranslationFiles(): void
    {
        $files = array_map('basename', glob(\dirname(__DIR__).'/translations/*') ?: []);

        $this->assertSame(array_map(static fn (string $locale): string => 'SebJeanZodiacBundle.'.$locale.'.xlf', self::LOCALES), $files);
    }

    #[DataProvider('provideLocales')]
    public function testEverySignIsTranslated(string $locale): void
    {
        // the loader also validates the file against the XLIFF schema
        $catalogue = new XliffFileLoader()->load(self::file($locale), $locale, 'SebJeanZodiacBundle');

        $expected = [
            ...array_map(static fn (WesternZodiacSign $sign): string => 'western.'.$sign->value, WesternZodiacSign::cases()),
            ...array_map(static fn (ChineseZodiacSign $sign): string => 'chinese.'.$sign->value, ChineseZodiacSign::cases()),
        ];
        $actual = array_keys($catalogue->all('SebJeanZodiacBundle'));
        sort($expected);
        sort($actual);

        $this->assertSame($expected, $actual);
    }

    public function testNorwegianAlias(): void
    {
        $this->assertFileEquals(self::file('nb'), self::file('no'), 'The NO locale should be an alias for the NB variant of the Norwegian language.');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideLocales(): iterable
    {
        foreach (self::LOCALES as $locale) {
            yield $locale => [$locale];
        }
    }

    private static function file(string $locale): string
    {
        return \dirname(__DIR__).'/translations/SebJeanZodiacBundle.'.$locale.'.xlf';
    }
}
