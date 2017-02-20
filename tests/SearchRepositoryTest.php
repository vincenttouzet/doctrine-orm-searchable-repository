<?php

/*
 * This file is part of the doctrine-orm-searchable-repository package.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\SAF\SearchableRepository;


use Tests\SAF\SearchableRepository\Entity\Book;

class SearchRepositoryTest extends AbstractTest
{
    public function testSearchByAssociation()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'author.firstName' => 'Lewis',
            'author.lastName' => 'Carroll',
        ]);

        $this->assertEquals(1, count($books), 'There must be 1 book written by Lewis Carroll');
    }

    /**
     * @expectedException \SAF\SearchableRepository\Exception\FieldNotFoundException
     */
    public function testFieldNotFoundException()
    {
        $repository = $this->getEntityRepository(Book::class);

        $repository->search([
            'unknown_field' => 'test',
        ]);
    }

    /**
     * @expectedException \SAF\SearchableRepository\Exception\AssociationNotFoundException
     */
    public function testAssociationNotFoundException()
    {
        $repository = $this->getEntityRepository(Book::class);

        $repository->search([
            'unknown_association.field' => 'test',
        ]);
    }
}
