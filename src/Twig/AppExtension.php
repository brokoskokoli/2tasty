<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig;

use App\Entity\RecipeIngredient;
use App\Entity\User;
use App\Service\IngredientService;
use App\Utils\Markdown;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This Twig extension adds a new 'md2html' filter to easily transform Markdown
 * contents into HTML contents inside Twig templates.
 *
 * See https://symfony.com/doc/current/cookbook/templating/twig_extension.html
 *
 * In addition to creating the Twig extension class, before using it you must also
 * register it as a service. See app/config/services.yml file for details.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Julien ITARD <julienitard@gmail.com>
 */
class AppExtension extends AbstractExtension
{
    private $parser;
    private $localeCodes;
    private $locales;


    /**
     * @var User
     */
    private $user;
    private $ingredientService;

    public function __construct(Markdown $parser,
                                TokenStorageInterface $tokenStorage,
                                IngredientService $ingredientService,
                                $locales)
    {
        $this->parser = $parser;
        if (!empty($tokenStorage->getToken())) {
            $this->user = $tokenStorage->getToken()->getUser();
        }
        $this->ingredientService = $ingredientService;
        $this->localeCodes = explode('|', $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('md2html', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
            new TwigFilter('ingredientText', [$this, 'ingredientText'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', [$this, 'getLocales']),
        ];
    }

    /**
     * Transforms the given Markdown content into HTML content.
     */
    public function markdownToHtml(string $content): string
    {
        return $this->parser->toHtml($content);
    }

    public function ingredientText(RecipeIngredient $recipeIngredient): string
    {

        $result = $this->ingredientService->getTranslatedCalculatedIngredientText($recipeIngredient, $this->user);

        if ($result != '') {
            $result .= ' - ';
        }

        if ($recipeIngredient->getText()) {
            $result .= $recipeIngredient->getText();
        }

        return $result;
    }

    /**
     * Takes the list of codes of the locales (languages) enabled in the
     * application and returns an array with the name of each locale written
     * in its own language (e.g. English, Français, Español, etc.).
     */
    public function getLocales(): array
    {
        if (null !== $this->locales) {
            return $this->locales;
        }

        $this->locales = [];
        foreach ($this->localeCodes as $localeCode) {
            $this->locales[] = ['code' => $localeCode, 'name' => Intl::getLocaleBundle()->getLocaleName($localeCode, $localeCode)];
        }

        return $this->locales;
    }
}
