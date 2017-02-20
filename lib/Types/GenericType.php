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
     * @throws ConditionNotSupportedException
     */
    public function addFilter(QueryBuilder $queryBuilder, $field, $condition, $value)
    {
        $parameter = sprintf(':%s_%s_value', str_replace('.', '_', $field), $condition);
        $expr = $queryBuilder->expr();
        switch ($condition) {
            case 'eq':
                $queryBuilder->andWhere($expr->eq($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'neq':
                $queryBuilder->andWhere($expr->neq($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'lt':
                $queryBuilder->andWhere($expr->lt($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'gt':
                $queryBuilder->andWhere($expr->gt($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'lte':
                $queryBuilder->andWhere($expr->lte($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'gte':
                $queryBuilder->andWhere($expr->gte($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'like':
                $queryBuilder->andWhere($expr->like($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'not_like':
                $queryBuilder->andWhere($expr->notLike($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'null':
                if ($value) {
                    $queryBuilder->andWhere($expr->isNull($field));
                } else {
                    $queryBuilder->andWhere($expr->isNotNull($field));
                }
                break;
            case 'not_null':
                if ($value) {
                    $queryBuilder->andWhere($expr->isNotNull($field));
                } else {
                    $queryBuilder->andWhere($expr->isNull($field));
                }
                break;
            case 'in':
                $queryBuilder->andWhere($expr->in($field, $parameter))
                    ->setParameter($parameter, $value);
                break;
            case 'not_in':
                $queryBuilder->andWhere($expr->notIn($field, $parameter))
                    ->setParameter($parameter, $value);
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
