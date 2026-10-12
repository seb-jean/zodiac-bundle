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

use SebJean\ZodiacBundle\Exception\InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Chinese zodiac sign.
 *
 * A sign starts on the Chinese New Year's Day and lasts until the next one. Dates
 * from 1900 to 2100 are supported.
 *
 * @author Sébastien Jean <sebastien.jean76@gmail.com>
 */
enum ChineseZodiacSign: string implements ZodiacSignInterface
{
    case Rat = 'rat';
    case Ox = 'ox';
    case Tiger = 'tiger';
    case Rabbit = 'rabbit';
    case Dragon = 'dragon';
    case Snake = 'snake';
    case Horse = 'horse';
    case Goat = 'goat';
    case Monkey = 'monkey';
    case Rooster = 'rooster';
    case Dog = 'dog';
    case Pig = 'pig';

    /**
     * Chinese New Year's Day of each year, as month and day: 131 is January 31st.
     */
    private const array NEW_YEARS = [
        1900 => 131, 1901 => 219, 1902 => 208, 1903 => 129, 1904 => 216, 1905 => 204, 1906 => 125, 1907 => 213, 1908 => 202, 1909 => 122,
        1910 => 210, 1911 => 130, 1912 => 218, 1913 => 206, 1914 => 126, 1915 => 214, 1916 => 203, 1917 => 123, 1918 => 211, 1919 => 201,
        1920 => 220, 1921 => 208, 1922 => 128, 1923 => 216, 1924 => 205, 1925 => 124, 1926 => 213, 1927 => 202, 1928 => 123, 1929 => 210,
        1930 => 130, 1931 => 217, 1932 => 206, 1933 => 126, 1934 => 214, 1935 => 204, 1936 => 124, 1937 => 211, 1938 => 131, 1939 => 219,
        1940 => 208, 1941 => 127, 1942 => 215, 1943 => 205, 1944 => 125, 1945 => 213, 1946 => 202, 1947 => 122, 1948 => 210, 1949 => 129,
        1950 => 217, 1951 => 206, 1952 => 127, 1953 => 214, 1954 => 203, 1955 => 124, 1956 => 212, 1957 => 131, 1958 => 218, 1959 => 208,
        1960 => 128, 1961 => 215, 1962 => 205, 1963 => 125, 1964 => 213, 1965 => 202, 1966 => 121, 1967 => 209, 1968 => 130, 1969 => 217,
        1970 => 206, 1971 => 127, 1972 => 215, 1973 => 203, 1974 => 123, 1975 => 211, 1976 => 131, 1977 => 218, 1978 => 207, 1979 => 128,
        1980 => 216, 1981 => 205, 1982 => 125, 1983 => 213, 1984 => 202, 1985 => 220, 1986 => 209, 1987 => 129, 1988 => 217, 1989 => 206,
        1990 => 127, 1991 => 215, 1992 => 204, 1993 => 123, 1994 => 210, 1995 => 131, 1996 => 219, 1997 => 207, 1998 => 128, 1999 => 216,
        2000 => 205, 2001 => 124, 2002 => 212, 2003 => 201, 2004 => 122, 2005 => 209, 2006 => 129, 2007 => 218, 2008 => 207, 2009 => 126,
        2010 => 214, 2011 => 203, 2012 => 123, 2013 => 210, 2014 => 131, 2015 => 219, 2016 => 208, 2017 => 128, 2018 => 216, 2019 => 205,
        2020 => 125, 2021 => 212, 2022 => 201, 2023 => 122, 2024 => 210, 2025 => 129, 2026 => 217, 2027 => 206, 2028 => 126, 2029 => 213,
        2030 => 203, 2031 => 123, 2032 => 211, 2033 => 131, 2034 => 219, 2035 => 208, 2036 => 128, 2037 => 215, 2038 => 204, 2039 => 124,
        2040 => 212, 2041 => 201, 2042 => 122, 2043 => 210, 2044 => 130, 2045 => 217, 2046 => 206, 2047 => 126, 2048 => 214, 2049 => 202,
        2050 => 123, 2051 => 211, 2052 => 201, 2053 => 219, 2054 => 208, 2055 => 128, 2056 => 215, 2057 => 204, 2058 => 124, 2059 => 212,
        2060 => 202, 2061 => 121, 2062 => 209, 2063 => 129, 2064 => 217, 2065 => 205, 2066 => 126, 2067 => 214, 2068 => 203, 2069 => 123,
        2070 => 211, 2071 => 131, 2072 => 219, 2073 => 207, 2074 => 127, 2075 => 215, 2076 => 205, 2077 => 124, 2078 => 212, 2079 => 202,
        2080 => 122, 2081 => 209, 2082 => 129, 2083 => 217, 2084 => 206, 2085 => 126, 2086 => 214, 2087 => 203, 2088 => 124, 2089 => 210,
        2090 => 130, 2091 => 218, 2092 => 207, 2093 => 127, 2094 => 215, 2095 => 205, 2096 => 125, 2097 => 212, 2098 => 201, 2099 => 121,
        2100 => 209,
    ];

    /**
     * Returns the sign of the given date.
     *
     * A DateTimeInterface is read in its own timezone. An int, a float, or a numeric
     * string is a Unix timestamp, read in the default timezone. Any other string is
     * parsed by DateTimeImmutable.
     *
     * @throws InvalidArgumentException When no date can be read from the value, or when the date is not from 1900 to 2100
     */
    public static function fromDate(\DateTimeInterface|\Stringable|string|int|float $date): self
    {
        $dateTime = DateConverter::toDateTime($date);
        $year = (int) $dateTime->format('Y');

        $newYear = self::NEW_YEARS[$year] ?? throw new InvalidArgumentException(\sprintf('The Chinese zodiac sign can only be computed from 1900 to 2100, %d given.', $year));

        // before the New Year's Day, the date still belongs to the previous year
        if ((int) $dateTime->format('md') < $newYear) {
            --$year;
        }

        // % keeps the sign of the dividend, adding 12 gives an index from 0 to 11 for any year
        return match ((($year - 4) % 12 + 12) % 12) {
            0 => self::Rat,
            1 => self::Ox,
            2 => self::Tiger,
            3 => self::Rabbit,
            4 => self::Dragon,
            5 => self::Snake,
            6 => self::Horse,
            7 => self::Goat,
            8 => self::Monkey,
            9 => self::Rooster,
            10 => self::Dog,
            11 => self::Pig,
        };
    }

    /**
     * Returns the emoji of the sign.
     */
    public function getEmoji(): string
    {
        return match ($this) {
            self::Rat => '🐀',
            self::Ox => '🐂',
            self::Tiger => '🐅',
            self::Rabbit => '🐇',
            self::Dragon => '🐉',
            self::Snake => '🐍',
            self::Horse => '🐎',
            self::Goat => '🐐',
            self::Monkey => '🐒',
            self::Rooster => '🐓',
            self::Dog => '🐕',
            self::Pig => '🐖',
        };
    }

    /**
     * Returns the name of the sign, in the given locale or in the one of the translator.
     */
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('chinese.'.$this->value, [], 'SebJeanZodiacBundle', $locale);
    }
}
