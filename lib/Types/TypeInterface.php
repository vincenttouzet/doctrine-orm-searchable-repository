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

interface TypeInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param string $field
     * @param string $condition
     * @param mixed $value
     *
     * @return mixed
     */
    public function addFilter(QueryBuilder $queryBuilder, $field, $condition, $value);

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $field
     * @param string $order
     */
    public function addOrder(QueryBuilder $queryBuilder, $field, $order);
}
