<?php

/*
 * This file is part of the doctrine-orm-searchable-repository project.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SAF\SearchableRepository\Types;

use Doctrine\ORM\QueryBuilder;
use SAF\SearchableRepository\Exception\ConditionNotSupportedException;
use SAF\SearchableRepository\Filter;

class GenericType implements TypeInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $field
     * @param string       $condition
     * @param mixed        $value
     *
     * @return mixed
     *
     * @throws ConditionNotSupportedException
     */
    public function addFilter(QueryBuilder $queryBuilder, $field, $condition, $value)
    {
        $parameter = sprintf(':%s_%s_value', str_replace('.', '_', $field), $condition);
        $expr = $queryBuilder->expr();
        switch ($condition) {
            case Filter::CONDITION_EQ:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->eq($field, $parameter);
                break;
            case Filter::CONDITION_NEQ:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->neq($field, $parameter);
                break;
            case Filter::CONDITION_LT:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->lt($field, $parameter);
                break;
            case Filter::CONDITION_GT:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->gt($field, $parameter);
                break;
            case Filter::CONDITION_LTE:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->lte($field, $parameter);
                break;
            case Filter::CONDITION_GTE:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->gte($field, $parameter);
                break;
            case Filter::CONDITION_LIKE:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->like($field, $parameter);
                break;
            case Filter::CONDITION_NOT_LIKE:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->notLike($field, $parameter);
                break;
            case Filter::CONDITION_NULL:
                if ($value) {
                    return $expr->isNull($field);
                } else {
                    return $expr->isNotNull($field);
                }
                break;
            case Filter::CONDITION_NOT_NULL:
                if ($value) {
                    return $expr->isNotNull($field);
                } else {
                    return $expr->isNull($field);
                }
                break;
            case Filter::CONDITION_IN:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->in($field, $parameter);
                break;
            case Filter::CONDITION_NOT_IN:
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
