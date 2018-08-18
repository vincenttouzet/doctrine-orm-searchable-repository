<?php

/*
 * This file is part of the doctrine-orm-searchable-repository project.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SAF\SearchableRepository\Exception;

class FieldOrAssociationNotFoundException extends \Exception
{
    public function __construct($entityName, $fieldName)
    {
        parent::__construct(sprintf('The entity "%s" has no field or association named "%s".', $entityName, $fieldName));
    }
}
