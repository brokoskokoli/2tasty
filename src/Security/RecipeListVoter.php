<?php

namespace App\Security;

use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RecipeListVoter extends Voter
{
    const EDIT = 'recipelist_edit';
    const DELETE = 'recipelist_delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        // this voter is only executed for three specific permissions on Recipe objects
        return $subject instanceof RecipeList && in_array($attribute, [self::EDIT, self::DELETE], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $recipeList, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }

        if ($recipeList->getAuthor() === null) {
            return true;
        }

        // the logic of this voter is pretty simple: if the logged user is the
        // author of the given blog recipe, grant permission; otherwise, deny it.
        // (the supports() method guarantees that $recipe is a Recipe object)
        return $user === $recipeList->getAuthor();
    }
}
