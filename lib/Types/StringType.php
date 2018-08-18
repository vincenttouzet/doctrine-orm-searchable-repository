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
use SAF\SearchableRepository\Filter;

class StringType extends GenericType
{
    public function addFilter(QueryBuilder $queryBuilder, $field, $condition, $value)
    {
        $parameter = sprintf(':%s_%s_value', str_replace('.', '_', $field), $condition);
        $expr = $queryBuilder->expr();
        switch ($condition) {
            case Filter::CONDITION_LIKE:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->like($expr->lower($field), $expr->lower($parameter));
                break;
            case Filter::CONDITION_NOT_LIKE:
                $queryBuilder->setParameter($parameter, $value);

                return $expr->notLike($expr->lower($field), $expr->lower($parameter));
                break;
            default:
                return parent::addFilter($queryBuilder, $field, $condition, $value);
        }
    }

    public function addOrder(QueryBuilder $queryBuilder, $field, $order)
    {
        $expr = $queryBuilder->expr();
        $queryBuilder->addOrderBy($expr->lower($field), $order);
    }
}
