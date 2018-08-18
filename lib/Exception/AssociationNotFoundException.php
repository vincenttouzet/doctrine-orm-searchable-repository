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

class AssociationNotFoundException extends \Exception
{
    public function __construct($entityName, $associationName)
    {
        parent::__construct(sprintf('The entity "%" does not have an association named "%s".', $entityName, $associationName));
    }
}
