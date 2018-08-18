<?php

/*
 * This file is part of the doctrine-orm-searchable-repository project.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SAF\SearchableRepository;

final class Filter
{
    const CONDITION_EQ = 'eq';
    const CONDITION_NEQ = 'neq';
    const CONDITION_LT = 'lt';
    const CONDITION_GT = 'gt';
    const CONDITION_LTE = 'lte';
    const CONDITION_GTE = 'gte';
    const CONDITION_BETWEEN = 'between';
    const CONDITION_LIKE = 'like';
    const CONDITION_NOT_LIKE = 'not_like';
    const CONDITION_NULL = 'null';
    const CONDITION_NOT_NULL = 'not_null';
    const CONDITION_IN = 'in';
    const CONDITION_NOT_IN = 'not_in';
}
