<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class IngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingredient::class);
    }

    public function getAllWithTranslationIn($locale)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->andWhere('i.'.$locale.' is not null');

        return $qb->orderBy('i.'.$locale, 'DESC')
        ->getQuery()
        ->getResult();
    }
}
