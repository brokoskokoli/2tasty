<?php

namespace App\Helper;

use Doctrine\ORM\QueryBuilder;

class QueryHelper
{
    public static function andWhereFromFilter(QueryBuilder &$qb, $filter, $name, $databaseField, $parameters = [])
    {
        $compareToNull = $parameters['compareToNull'] ?? false;
        $caseInsensitiv = $parameters['caseInsensitiv'] ?? false;
        $varname = $name.'_'.str_replace('.', '_', $databaseField);
        if (isset($filter[$name]) && ($filter[$name] || $compareToNull)) {
            $value = $filter[$name];
            if (is_object($value) && method_exists($value, 'getId')) {
                $value = $value->getId();
            }
            if ($caseInsensitiv) {
                $qb->andWhere("upper($databaseField) = upper(:$varname)");
            } else {
                $qb->andWhere("$databaseField = :$varname");
            }
            $qb->setParameter($varname, $value);
        }
    }

    public static function andWhereLikeFromFilter(QueryBuilder &$qb, $filter, $name, $databaseField, $parameters = [])
    {
        $compareToNull = $parameters['compareToNull'] ?? false;
        $caseInsensitiv = $parameters['caseInsensitiv'] ?? false;
        $varname = $name.'_'.str_replace('.', '_', $databaseField);
        if (isset($filter[$name]) && ($filter[$name] || $compareToNull)) {
            $value = $filter[$name];
            if (is_object($value) && method_exists($value, 'getId')) {
                $value = $value->getId();
            }
            if ($caseInsensitiv) {
                $qb->andWhere("upper($databaseField) LIKE upper(:$varname)");
            } else {
                $qb->andWhere("$databaseField = :$varname");
            }
            $qb->setParameter($varname, '%' . $value . '%');
        }
    }

    public static function andWhereNotInFromFilter(QueryBuilder &$qb, $filter, $name, $databaseField)
    {
        $varname = $name.'_'.str_replace('.', '_', $databaseField);
        if ($filter[$name] ?? false) {
            $value = $filter[$name];
            if (is_object($value) && method_exists($value, 'getId')) {
                $value = $value->getId();
            }
            $qb->andWhere("$databaseField NOT IN (:$varname)")
                ->setParameter($varname, $value);
        }
    }

    public static function andWhereInFromFilter(QueryBuilder &$qb, $filter, $name, $databaseField)
    {
        $varname = $name.'_'.str_replace('.', '_', $databaseField);
        if ($filter[$name] ?? false) {
            $qb->andWhere("$databaseField IN (:$varname)")
                ->setParameter($varname, $filter[$name]);
        }
    }

    public static function setAndIsNull(QueryBuilder &$qb, $filter, $name)
    {
        if ($filter[$name] ?? false) {
            foreach ($filter[$name] as $champ) {
                $qb->andWhere("$champ IS NULL");
            }
        }
    }

    public static function setAndIsNullByValue(QueryBuilder &$qb, $filter, $name, $databaseField)
    {
        if (isset($filter[$name])) {
            if (boolval($filter[$name])) {
                $qb->andWhere("$databaseField IS NULL");
            } else {
                $qb->andWhere("$databaseField IS NOT NULL");
            }
        }
    }

    public static function setAndIsNotNull(QueryBuilder &$qb, $filter, $name)
    {
        if ($filter[$name] ?? false) {
            foreach ($filter[$name] as $champ) {
                $qb->andWhere("$champ IS NOT NULL");
            }
        }
    }

    public static function sortQuery(QueryBuilder $qb, $filter, $name)
    {
        if ($filter[$name]['sort'] ?? false && $filter[$name]['orderBy'] ?? false) {
            self::sortQueryDirect($qb, $filter[$name]['sort'], $filter[$name]['orderBy'], null, 'ASC');
        }
    }

    public static function sortQueryDirect(QueryBuilder $qb, $sort, $orderBy, $sortDefault, $orderByDefault = 'ASC')
    {
        if (!$sort || !$orderBy) {
            $qb->orderBy($sortDefault, $orderByDefault);
        } else {
            $qb->orderBy($sort, $orderBy);
        }
    }

    public static function setLimitAndPage(QueryBuilder &$qb, $limit, $page)
    {
        if ($limit) {
            $qb->setMaxResults($limit);
            if ($page) {
                $offset = ($page - 1) * $limit;
                $qb->setFirstResult($offset);
            }
        }
    }
}
