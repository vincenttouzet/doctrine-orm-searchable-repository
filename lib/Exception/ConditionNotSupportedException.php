<?php

/*
 * This file is part of the doctrine-orm-searchable-repository package.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SAF\SearchableRepository\Exception;

use Exception;

class ConditionNotSupportedException extends \Exception
{
    public function __construct($condition, $type)
    {
        parent::__construct(sprintf('The condition "%s" is not supported for the "%s" type.', $condition, $type));
    }
}
