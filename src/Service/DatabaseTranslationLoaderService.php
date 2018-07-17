<?php

namespace App\Service;

use App\Service\IngredientService;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Loader\LoaderInterface;

class DatabaseTranslationLoaderService implements LoaderInterface
{
   /**
     * @var IngredientService
     */
    private $ingredientService;

    /**
     * @var RefUnitService
     */
    private $refUnitService;

    public function __construct(IngredientService $ingredientService, RefUnitService$refUnitService)
    {
        $this->ingredientService = $ingredientService;
        $this->refUnitService = $refUnitService;
    }


    public function load($resource, $locale, $domain = 'messages')
    {
        $messages = array();
        $ingredients = $this->ingredientService->getAll();

        $function = 'get' . $locale;
        foreach ($ingredients as $ingredient) {
            $messages[$ingredient->getName()] = $ingredient->$function();
        }

        $units = $this->refUnitService->getAll();

        $function = 'get' . $locale;
        foreach ($units as $unit) {
            $messages[$unit->getName()] = $unit->$function();
        }

        $messageCatalogue = new MessageCatalogue($locale);
        $messageCatalogue->add($messages, $domain);

        return $messageCatalogue;
    }

}
