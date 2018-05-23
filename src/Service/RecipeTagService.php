<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\RecipeTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RecipeTagService
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
        return $this->em->getRepository(RecipeTag::class)->findAll();
    }

    public function getAllNames()
    {
        return array_map(function ($element) {
            return $element->getName();
        }, $this->getAll());
    }
}
