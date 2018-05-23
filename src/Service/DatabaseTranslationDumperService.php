<?php

namespace App\Service;

use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\MessageCatalogue;

class DatabaseTranslationDumperService  implements DumperInterface
{
   /**
     * @var IngredientService
     */
    private $ingredientService;

    public function __construct(IngredientService $ingredientService)
    {
        $this->ingredientService = $ingredientService;
    }

    public function dump(MessageCatalogue $messages, $options = array())
    {

    }


}
