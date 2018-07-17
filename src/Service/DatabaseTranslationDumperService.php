<?php

namespace App\Service;

use Symfony\Component\Translation\Dumper\DumperInterface;
use Symfony\Component\Translation\MessageCatalogue;

class DatabaseTranslationDumperService  implements DumperInterface
{

    public function __construct(IngredientService $ingredientService, RefUnitService$refUnitService)
    {
    }

    public function dump(MessageCatalogue $messages, $options = array())
    {

    }


}
