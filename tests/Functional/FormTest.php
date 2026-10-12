<?php

/*
 * This file is part of the seb-jean/zodiac-bundle package.
 *
 * (c) Sébastien Jean <sebastien.jean76@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebJean\ZodiacBundle\Tests\Functional;

use SebJean\ZodiacBundle\Sign\WesternZodiacSign;
use SebJean\ZodiacBundle\Tests\Fixtures\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Twig\Environment;

final class FormTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testEnumTypeUsesTheTranslatedNamesAsLabels(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $translator = $container->get('translator');
        $this->assertInstanceOf(LocaleAwareInterface::class, $translator);
        $translator->setLocale('fr');

        $formFactory = $container->get('form.factory');
        $this->assertInstanceOf(FormFactoryInterface::class, $formFactory);
        $form = $formFactory->createNamed('sign', EnumType::class, WesternZodiacSign::Aries, ['class' => WesternZodiacSign::class]);

        $twig = $container->get('twig');
        $this->assertInstanceOf(Environment::class, $twig);
        $html = $twig->createTemplate('{{ form_widget(form) }}')->render(['form' => $form->createView()]);

        $this->assertStringContainsString('<option value="aries" selected="selected">Bélier</option>', $html);
        $this->assertStringContainsString('<option value="pisces">Poissons</option>', $html);
    }
}
