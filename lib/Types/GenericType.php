<?php

/*
 * This file is part of the doctrine-orm-searchable-repository package.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SAF\SearchableRepository\Types;

use Doctrine\ORM\QueryBuilder;
use SAF\SearchableRepository\Exception\ConditionNotSupportedException;

class GenericType implements TypeInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param string $field
     * @param string $condition
     * @param mixed $value
     * @return mixed
     * @throws ConditionNotSupportedException
     */
    public function addFilter(QueryBuilder $queryBuilder, $field, $condition, $value)
    {
        $parameter = sprintf(':%s_%s_value', str_replace('.', '_', $field), $condition);
        $expr = $queryBuilder->expr();
        switch ($condition) {
            case 'eq':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->eq($field, $parameter);
                break;
            case 'neq':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->neq($field, $parameter);
                break;
            case 'lt':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->lt($field, $parameter);
                break;
            case 'gt':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->gt($field, $parameter);
                break;
            case 'lte':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->lte($field, $parameter);
                break;
            case 'gte':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->gte($field, $parameter);
                break;
            case 'like':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->like($field, $parameter);
                break;
            case 'not_like':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->notLike($field, $parameter);
                break;
            case 'null':
                if ($value) {
                    return $expr->isNull($field);
                } else {
                    return $expr->isNotNull($field);
                }
                break;
            case 'not_null':
                if ($value) {
                    return $expr->isNotNull($field);
                } else {
                    return $expr->isNull($field);
                }
                break;
            case 'in':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->in($field, $parameter);
                break;
            case 'not_in':
                $queryBuilder->setParameter($parameter, $value);
                return $expr->notIn($field, $parameter);
                break;
            default:
                throw new ConditionNotSupportedException($condition, self::class);
        }
    }

    public function addOrder(QueryBuilder $queryBuilder, $field, $order)
    {
        $queryBuilder->addOrderBy($field, $order);
    }
}
