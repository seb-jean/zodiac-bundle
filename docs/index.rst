SebJeanZodiacBundle: zodiac signs for Symfony
=============================================

This bundle gives the western or Chinese zodiac sign of a date in your Symfony
application, with its emoji and its translated name.

Installation
------------

Make sure Composer is installed globally, as explained in the
`installation chapter`_ of the Composer documentation.

Applications that use Symfony Flex
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console, enter your project directory and execute:

.. code-block:: terminal

    composer require seb-jean/zodiac-bundle

Applications that don't use Symfony Flex
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Step 1: Download the Bundle
...........................

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: terminal

    composer require seb-jean/zodiac-bundle

Step 2: Enable the Bundle
.........................

Then, enable the bundle by adding it to the list of registered bundles
in the ``config/bundles.php`` file of your project::

    // config/bundles.php
    return [
        // ...
        SebJean\ZodiacBundle\SebJeanZodiacBundle::class => ['all' => true],
    ];

Usage
-----

Service
~~~~~~~

Inject the ``ZodiacCalculator`` service, in a controller for instance::

    use SebJean\ZodiacBundle\Calculator\Astrology;
    use SebJean\ZodiacBundle\Calculator\ZodiacCalculator;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Contracts\Translation\TranslatorInterface;

    public function profile(ZodiacCalculator $zodiac, TranslatorInterface $translator): Response
    {
        $sign = $zodiac->calculate('1990-03-25');                     // WesternZodiacSign::Aries
        $sign = $zodiac->calculate('1990-03-25', Astrology::Chinese); // ChineseZodiacSign::Horse

        $sign->getEmoji();               // '🐎'
        $sign->trans($translator);       // 'Horse', in the current locale
        $sign->trans($translator, 'fr'); // 'Cheval'

        // ...
    }

The date can be any of the `Accepted Dates`_, and the astrology can also be
given as a string: ``'western'`` (the default) or ``'chinese'``.

The signs are enums, which can also be used without the service::

    use SebJean\ZodiacBundle\Sign\ChineseZodiacSign;
    use SebJean\ZodiacBundle\Sign\WesternZodiacSign;

    WesternZodiacSign::fromDate('1990-03-25'); // WesternZodiacSign::Aries
    ChineseZodiacSign::fromDate('1990-03-25'); // ChineseZodiacSign::Horse

Both enums implement ``SebJean\ZodiacBundle\Sign\ZodiacSignInterface``, which
provides ``getEmoji()`` and extends Symfony's ``TranslatableInterface`` for the
name.

Twig
~~~~

.. code-block:: twig

    {{ date|zodiac_sign(astrology = 'western') }}
    {{ zodiac_sign(date, astrology = 'western') }}

``date``
    **type**: ``DateTimeInterface`` | ``Stringable`` | ``string`` | ``int`` | ``float``
``astrology`` *(optional)*
    **type**: ``string`` **default**: ``western`` **possible values**: ``western`` or ``chinese``

The ``zodiac_sign`` filter and function return the sign of a date, which can
be any of the `Accepted Dates`_. For a user born on March 25, 1990:

.. code-block:: twig

    {# with the filter #}
    {% set sign = user.birthdate|zodiac_sign %}

    {# ... or the equivalent function #}
    {% set sign = zodiac_sign(user.birthdate) %}

    Your zodiac sign is {{ sign.emoji }} {{ sign|trans }}
    {# outputs "Your zodiac sign is ♈ Aries" #}

    {# the name in a specific locale #}
    {{ sign|trans(locale: 'fr') }}
    {# outputs "Bélier" #}

    {# the Chinese zodiac #}
    {% set sign = user.birthdate|zodiac_sign(astrology: 'chinese') %}

    Your Chinese zodiac sign is {{ sign.emoji }} {{ sign|trans }}
    {# outputs "Your Chinese zodiac sign is 🐎 Horse" #}

.. note::

    Use the ``trans`` filter to display the name of a sign: ``sign.name``
    returns the name of the enum case (``Aries``), which is not translated.

Twig is optional: the filter and the function are only registered when
TwigBundle is enabled.

Forms
~~~~~

The ``EnumType`` form type lists the signs, with their translated names as
labels::

    use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
    use Symfony\Component\Form\Extension\Core\Type\EnumType;

    $builder->add('sign', EnumType::class, [
        'class' => WesternZodiacSign::class,
    ]);

Routing
~~~~~~~

A route parameter is converted to a sign, and a 404 error is returned when the
value is not a valid sign::

    use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;

    #[Route('/signs/{sign}')]
    public function show(WesternZodiacSign $sign): Response
    {
        // /signs/aries gives WesternZodiacSign::Aries
    }

Serializer
~~~~~~~~~~

The serializer writes a sign as its value, and reads it back::

    use SebJean\ZodiacBundle\Sign\WesternZodiacSign;

    $serializer->serialize(WesternZodiacSign::Aries, 'json');          // "aries"
    $serializer->deserialize('"aries"', WesternZodiacSign::class, 'json'); // WesternZodiacSign::Aries

Doctrine
~~~~~~~~

A sign can be stored in an entity::

    use Doctrine\ORM\Mapping as ORM;
    use SebJean\ZodiacBundle\Sign\WesternZodiacSign;

    #[ORM\Column(enumType: WesternZodiacSign::class)]
    private ?WesternZodiacSign $sign = null;

Accepted Dates
--------------

``ZodiacCalculator::calculate()``, the ``fromDate()`` methods of the signs and
the ``zodiac_sign`` filter and function accept the same values:

.. list-table::
    :header-rows: 1

    * - Value
      - Read as
    * - ``DateTimeInterface``
      - the date itself, in its own timezone
    * - ``int``, ``float`` or numeric ``string``
      - a Unix timestamp, in the default timezone (``date_default_timezone_get()``)
    * - other ``string`` or ``Stringable``
      - a date parsed by ``DateTimeImmutable``: ``'1990-03-25'``,
        ``'first day of june 1990'``, ``'now'``...

A numeric string is a timestamp, as with the ``date`` filter of Twig:
``'19900325'`` is a date in August 1970, not March 25, 1990.

A ``SebJean\ZodiacBundle\Exception\InvalidArgumentException`` is thrown when no
date can be read from the value, when the date does not exist, like
``'1990-04-31'``, when the astrology is unknown, or when the date is out of the
range supported by the Chinese zodiac. All the exceptions of the bundle
implement ``SebJean\ZodiacBundle\Exception\ExceptionInterface``.

.. note::

    ``null`` is not accepted, and an empty string is not read as the current
    date: check optional dates, like a birthdate that may not be filled in,
    before computing their sign.

Western Zodiac
--------------

Each sign starts and ends on a fixed day, both included, whatever the time:

.. list-table::
    :header-rows: 1

    * - Sign
      - From
      - To
    * - ♈ ``WesternZodiacSign::Aries``
      - March 21
      - April 20
    * - ♉ ``WesternZodiacSign::Taurus``
      - April 21
      - May 21
    * - ♊ ``WesternZodiacSign::Gemini``
      - May 22
      - June 21
    * - ♋ ``WesternZodiacSign::Cancer``
      - June 22
      - July 22
    * - ♌ ``WesternZodiacSign::Leo``
      - July 23
      - August 23
    * - ♍ ``WesternZodiacSign::Virgo``
      - August 24
      - September 23
    * - ♎ ``WesternZodiacSign::Libra``
      - September 24
      - October 23
    * - ♏ ``WesternZodiacSign::Scorpio``
      - October 24
      - November 22
    * - ♐ ``WesternZodiacSign::Sagittarius``
      - November 23
      - December 21
    * - ♑ ``WesternZodiacSign::Capricorn``
      - December 22
      - January 20
    * - ♒ ``WesternZodiacSign::Aquarius``
      - January 21
      - February 19
    * - ♓ ``WesternZodiacSign::Pisces``
      - February 20
      - March 20

The ``getStartDate()`` and ``getEndDate()`` methods return the first and last
days of a sign in the current year, at midnight in the default timezone.
Capricorn starts in December and ends in January of the next year.

Chinese Zodiac
--------------

Each sign starts on a Chinese New Year's Day, between January 21 and
February 20, and lasts until the next one. The signs repeat every 12 years,
for instance from 2020 to 2031:

.. list-table::
    :header-rows: 1

    * - Sign
      - From
      - To
    * - 🐀 ``ChineseZodiacSign::Rat``
      - January 25, 2020
      - February 11, 2021
    * - 🐂 ``ChineseZodiacSign::Ox``
      - February 12, 2021
      - January 31, 2022
    * - 🐅 ``ChineseZodiacSign::Tiger``
      - February 1, 2022
      - January 21, 2023
    * - 🐇 ``ChineseZodiacSign::Rabbit``
      - January 22, 2023
      - February 9, 2024
    * - 🐉 ``ChineseZodiacSign::Dragon``
      - February 10, 2024
      - January 28, 2025
    * - 🐍 ``ChineseZodiacSign::Snake``
      - January 29, 2025
      - February 16, 2026
    * - 🐎 ``ChineseZodiacSign::Horse``
      - February 17, 2026
      - February 5, 2027
    * - 🐐 ``ChineseZodiacSign::Goat``
      - February 6, 2027
      - January 25, 2028
    * - 🐒 ``ChineseZodiacSign::Monkey``
      - January 26, 2028
      - February 12, 2029
    * - 🐓 ``ChineseZodiacSign::Rooster``
      - February 13, 2029
      - February 2, 2030
    * - 🐕 ``ChineseZodiacSign::Dog``
      - February 3, 2030
      - January 22, 2031
    * - 🐖 ``ChineseZodiacSign::Pig``
      - January 23, 2031
      - February 10, 2032

Dates from 1900-01-01 to 2100-12-31 are supported. The New Year's Days of the
bundle are checked against the new moons computed with the algorithm of Jean
Meeus, *Astronomical Algorithms*, chapter 49.

Translations
------------

The names of the signs are translated into the same languages as Symfony, in
the ``SebJeanZodiacBundle`` domain, and need the Translation component
(``composer require symfony/translation``). An application can change a name
or add a language in its own translation files, with ids like
``western.aries`` or ``chinese.rat``:

.. code-block:: yaml

    # translations/SebJeanZodiacBundle.ko.yaml
    western.aries: 양자리

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
