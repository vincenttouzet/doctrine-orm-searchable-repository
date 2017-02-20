<?php

/*
 * This file is part of the doctrine-orm-searchable-repository package.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SAF\SearchableRepository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use SAF\SearchableRepository\Exception\AssociationNotFoundException;
use SAF\SearchableRepository\Exception\FieldNotFoundException;
use SAF\SearchableRepository\Types\GenericType;
use SAF\SearchableRepository\Types\TypeInterface;

class SearchableRepository extends EntityRepository
{
    /** @var TypeInterface */
    protected $defaultType;

    /** @var TypeInterface[] */
    protected $types;

    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->init();
    }

    public function init()
    {
        $this->defaultType = new GenericType();
    }

    /**
     * Process a search on the repository
     * @param array $filters
     * @param array $orders
     *
     * @return mixed
     */
    public function search(array $filters = [], array $orders = [])
    {
        return $this->getSearchQueryBuilder($filters, $orders)->getQuery()->execute();
    }

    /**
     * @param array $filters
     * @param array $orders
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getSearchQueryBuilder(array $filters = [], array $orders = [])
    {
        $queryBuilder = $this->createQueryBuilder('main');

        $classMetadata = $this->getClassMetadata();

        $fieldMappings = [];
        // make necessary joins
        foreach (array_merge(array_keys($filters), array_keys($orders)) as $field) {
            $fieldMappings = array_merge(
                $fieldMappings,
                $this->getFieldMappings($queryBuilder, $field, $classMetadata)
            );
        }

        // filters
        foreach ($filters as $field => $filter) {
            $filterCondition = null; // confition of filter : eq, neq, gt, lt, ...
            $filterValue = $filter; // value to filter
            if (is_array($filter)) {
                $filterCondition = key($filter);
                $filterValue = current($filter);
            } else {
                $filterValue = $filter;
            }
            if (!$filterCondition) {
                $filterCondition = 'eq';
            }

            $fieldMapping = isset($fieldMappings[$field]) ? $fieldMappings[$field] : $fieldMappings['main.'.$field];
            $type = $this->getType($fieldMapping['mapping']['type']);
            $type->addFilter($queryBuilder, $fieldMapping['queryAlias'], $filterCondition, $filterValue);
        }

        // orders
        foreach ($orders as $field => $order) {
            $fieldMapping = isset($fieldMappings[$field]) ? $fieldMappings[$field] : $fieldMappings['main.'.$field];
            $type = $this->getType($fieldMapping['mapping']['type']);
            $type->addOrder($queryBuilder, $fieldMapping['queryAlias'], $order);
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $field
     * @param ClassMetadataInfo $classMetadata
     * @param string $previous
     * @return array
     * @throws AssociationNotFoundException
     * @throws FieldNotFoundException
     */
    protected function getFieldMappings(QueryBuilder $queryBuilder, $field, ClassMetadataInfo $classMetadata, $previous = 'main')
    {
        $fieldMappings = [];
        if (strstr($field, '.') !== false) {
            $parts = explode('.', $field);
            $associationName = array_shift($parts);
            $associationField = implode('.', $parts);
            if (!$classMetadata->hasAssociation($associationName)) {
                throw new AssociationNotFoundException($classMetadata->getName(), $associationName);
            }
            // add join if not already
            if (!$this->hasJoinInQueryBuilder($queryBuilder, $previous.'_'.$associationName, $previous)) {
                $queryBuilder->innerJoin($previous.'.'.$associationName, $previous.'_'.$associationName);
            }
            $association = $classMetadata->getAssociationMapping($associationName);
            $associationClassMetadata = $this->getEntityManager()->getClassMetadata($association['targetEntity']);
            $fieldMappings = array_merge(
                $fieldMappings,
                $this->getFieldMappings(
                    $queryBuilder,
                    $associationField,
                    $associationClassMetadata,
                    $previous.'.'.$associationName
                )
            );
        } else {
            // check if field exist
            if (!$classMetadata->hasField($field)) {
                throw new FieldNotFoundException($classMetadata->getName(), $field);
            }
            // add field mapping
            $fieldMappings[$previous.'.'.$field] = [
                'mapping' => $classMetadata->getFieldMapping($field),
                'queryAlias' => str_replace('.', '_', $previous).'.'.$field
            ];
        }

        return $fieldMappings;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string $alias Alias of the join (e.g: main_author)
     * @param string $entityAlias Alias of the entity (e.g: main)
     * @return bool
     */
    private function hasJoinInQueryBuilder(QueryBuilder $queryBuilder, $alias, $entityAlias)
    {
        foreach ($queryBuilder->getDQLPart('join') as $joinEntityAlias => $joins) {
            if ($joinEntityAlias === $entityAlias) {
                /** @var \Doctrine\ORM\Query\Expr\Join $join */
                foreach ($joins as $join) {
                    if ($join->getAlias() === $alias) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @param TypeInterface $type
     * @return $this
     */
    public function setType($name, TypeInterface $type)
    {
        $this->types[$name] = $type;

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasType($name)
    {
        return isset($this->types[$name]);
    }

    /**
     * @param $name
     * @return TypeInterface
     */
    public function getType($name)
    {
        if ($this->hasType($name)) {
            return $this->types[$name];
        }

        return $this->defaultType;
    }
}
