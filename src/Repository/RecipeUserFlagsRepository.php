<?php

namespace App\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\RecipeUserFlags;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class RecipeUserFlagsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeUserFlags::class);
    }

    /**
     * @author Stefan RICHTER <srichter@webnet.fr>
     * @param Recipe $recipe
     * @param User $user
     * @return RecipeUserFlags|null
     */
    public function getRecipeUserFlags(Recipe $recipe, User $user)
    {
        $recipeTags = $this->findOneBy(['author' => $user, 'recipe' => $recipe]);
        if (!$recipeTags) {
            $recipeTags = new RecipeUserFlags();
            $recipeTags->setAuthor($user);
            $recipeTags->setRecipe($recipe);
            $this->_em->persist($recipeTags);
        }

        return $recipeTags;
    }
}
