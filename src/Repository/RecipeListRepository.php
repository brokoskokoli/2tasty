<?php

namespace App\Repository;

use App\Entity\RecipeList;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class RecipeListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeList::class);
    }


    /**
     * @return RecipeList[]|array
     */
    public function getAllForUser(?User $user = null)
    {
        $queryBuilder = $this->createQueryBuilder('rl');
        $queryBuilder->andWhere('rl.author = :user or rl.author is null');

        $queryBuilder->setParameter('user', $user);

        return $queryBuilder->getQuery()->getResult();
    }
}
