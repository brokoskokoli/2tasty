<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use App\Entity\RefUnit;
use App\Entity\RefUnitName;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Webit\Util\EvalMath\EvalMath;

class RefUnitService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /** @var TranslatorInterface $translator */
    private $translator;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ) {
        $this->em = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @return Ingredient[]|array
     */
    public function getAll()
    {
        return $this->em->getRepository(RefUnit::class)->findAll();
    }

    public function getAllNames()
    {
        return array_map(function ($element) {
            return $element->getName();
        }, $this->getAll());
    }

    public function getUnitTextsForLanguage($languageString = 'en')
    {
        $units = $this->em->getRepository(RefUnit::class)->findAll();
        $result = [];
        foreach ($units as $unit) {
            $function = 'get' . $languageString;
            if ($unit->$function()) {
                $result[$unit->$function()] = $unit;
            }
        }
        $unitNames = $this->em->getRepository(RefUnitName::class)->findBy([
            'language' => $languageString,
        ]);
        foreach ($unitNames as $unitName) {
            if ($unitName->getName()) {
                $result[$unitName->getName()] = $unitName;
            }
        }

        return $result;
    }
}
