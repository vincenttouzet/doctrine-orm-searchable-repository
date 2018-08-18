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

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\QueryBuilder;
use SAF\SearchableRepository\Exception\AssociationNotFoundException;
use SAF\SearchableRepository\Exception\FieldOrAssociationNotFoundException;
use SAF\SearchableRepository\Types\GenericType;
use SAF\SearchableRepository\Types\StringType;
use SAF\SearchableRepository\Types\TypeInterface;

trait SearchableRepositoryTrait
{
    /** @var TypeInterface */
    protected $defaultType;

    /** @var TypeInterface[] */
    protected $types;

    public function init()
    {
        $this->defaultType = new GenericType();
        $this->setType('string', new StringType());
        $this->setType('text', new StringType());
    }

    /**
     * Process a search on the repository.
     *
     * @param array $filters
     * @param array $orders
     *
     * @return mixed
     *
     * @throws AssociationNotFoundException
     * @throws FieldOrAssociationNotFoundException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function search(array $filters = [], array $orders = [])
    {
        return $this->getSearchQueryBuilder($filters, $orders)->getQuery()->execute();
    }

    /**
     * @param array $filters
     * @param array $orders
     *
     * @return \Doctrine\ORM\QueryBuilder
     *
     * @throws AssociationNotFoundException
     * @throws FieldOrAssociationNotFoundException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function getSearchQueryBuilder(array $filters = [], array $orders = [])
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('main');

        $classMetadata = $this->getClassMetadata();

        $fieldMappings = [];
        // make necessary joins
        foreach ($this->getFields($filters, $orders) as $field) {
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
                if (isset($filter['field'])) {
                    $field = $filter['field'];
                    $filterCondition = $filter['condition'];
                    $filterValue = $filter['value'];
                } else {
                    $filterCondition = key($filter);
                    $filterValue = current($filter);
                }
            } else {
                $filterValue = $filter;
            }
            if (!$filterCondition) {
                $filterCondition = 'eq';
            }

            if (is_array($field)) {
                $expr = $queryBuilder->expr()->orX();
                foreach ($field as $f) {
                    $fieldMapping = isset($fieldMappings[$f]) ? $fieldMappings[$f] : $fieldMappings['main.'.$f];
                    $type = $this->getType($fieldMapping['mapping']['type']);
                    $expr->add($type->addFilter($queryBuilder, $fieldMapping['queryAlias'], $filterCondition, $filterValue));
                }
            } else {
                $fieldMapping = isset($fieldMappings[$field]) ? $fieldMappings[$field] : $fieldMappings['main.'.$field];
                $type = $this->getType($fieldMapping['mapping']['type']);
                $expr = $type->addFilter($queryBuilder, $fieldMapping['queryAlias'], $filterCondition, $filterValue);
            }
            $queryBuilder->andWhere($expr);
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
     * @param array $filters
     * @param array $orders
     *
     * @return array|int|string
     */
    protected function getFields(array $filters = [], array $orders = [])
    {
        $fields = array_keys($orders);

        foreach ($filters as $field => $filter) {
            // full notation ?
            if (is_array($filter) && isset($filter['field'])) {
                $fields[] = $filter['field'];
            } else {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    /**
     * @param QueryBuilder      $queryBuilder
     * @param                   $field
     * @param ClassMetadataInfo $classMetadata
     * @param string            $previous
     *
     * @return array
     *
     * @throws AssociationNotFoundException
     * @throws FieldOrAssociationNotFoundException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    protected function getFieldMappings(QueryBuilder $queryBuilder, $field, ClassMetadataInfo $classMetadata, $previous = 'main')
    {
        $fieldMappings = [];
        if (is_array($field)) {
            foreach ($field as $f) {
                $fieldMappings = array_merge(
                    $fieldMappings,
                    $this->getFieldMappings($queryBuilder, $f, $classMetadata, $previous)
                );
            }

            return $fieldMappings;
        }
        if (false !== strstr($field, '.')) {
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
            // check if field or association exist
            // add field mapping
            if ($classMetadata->hasField($field)) {
                $fieldMappings[$previous.'.'.$field] = [
                    'mapping' => $classMetadata->getFieldMapping($field),
                    'queryAlias' => str_replace('.', '_', $previous).'.'.$field,
                ];
            } elseif ($classMetadata->hasAssociation($field)) {
                $fieldMappings[$previous.'.'.$field] = [
                    'mapping' => $classMetadata->getAssociationMapping($field),
                    'queryAlias' => str_replace('.', '_', $previous).'.'.$field,
                ];
            } else {
                throw new FieldOrAssociationNotFoundException($classMetadata->getName(), $field);
            }
        }

        return $fieldMappings;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string       $alias        Alias of the join (e.g: main_author)
     * @param string       $entityAlias  Alias of the entity (e.g: main)
     *
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
     * @param string        $name
     * @param TypeInterface $type
     *
     * @return $this
     */
    public function setType($name, TypeInterface $type)
    {
        $this->types[$name] = $type;

        return $this;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasType($name)
    {
        return isset($this->types[$name]);
    }

    /**
     * @param $name
     *
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
