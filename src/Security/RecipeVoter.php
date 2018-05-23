<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to blog recipes (such as
 * showing, editing and deleting recipes).
 *
 * See https://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class RecipeVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        // this voter is only executed for three specific permissions on Recipe objects
        return $subject instanceof Recipe && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $recipe, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }

        // the logic of this voter is pretty simple: if the logged user is the
        // author of the given blog recipe, grant permission; otherwise, deny it.
        // (the supports() method guarantees that $recipe is a Recipe object)
        return $user === $recipe->getAuthor();
    }
}
