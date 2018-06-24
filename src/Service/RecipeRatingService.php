<?php

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeRating;
use App\Entity\RecipeTag;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RecipeRatingService
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

    public function saveRating(RecipeRating $rating)
    {
        $this->em->getRepository(RecipeRating::class)->disableAllOldRatings($rating->getRecipe(), $rating->getAuthor());
        $this->em->persist($rating);
        $this->em->flush();
    }

    public function getRating(Recipe $recipe, User $user) : ?RecipeRating
    {
        $userRating = $this->em->getRepository(RecipeRating::class)->getRatingFromUser($recipe, $user);
        if (!$userRating) {
            return $this->em->getRepository(RecipeRating::class)->getRatingGlobal($recipe);
        }

        return $userRating;
    }

    public function getRatingFromUser(Recipe $recipe, User $user) : ?RecipeRating
    {
        return $this->em->getRepository(RecipeRating::class)->getRatingFromUser($recipe, $user);
    }

    public function getRatingGlobal(Recipe $recipe)
    {
        $list = $this->em->getRepository(RecipeRating::class)->getRatingGlobal($recipe);

        if (count($list) == 0) {
            return null;
        }

        $sum = 0;
        $count = 0;
        foreach ($list as $rating) {
            if ($rating->isEnabled()) {
                $sum += $rating->getRating();
                $count++;
            }
        }

        return floatval($sum)/floatval($count);
    }
}
