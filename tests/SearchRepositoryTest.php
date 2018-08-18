<?php

/*
 * This file is part of the doctrine-orm-searchable-repository project.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\SAF\SearchableRepository;

use Tests\SAF\SearchableRepository\Entity\Author;
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

    public function testSearchMultipleField()
    {
        $repository = $this->getEntityRepository(Author::class);

        $authors = $repository->search([
            'name' => [
                'field' => ['firstName', 'lastName'],
                'condition' => 'eq',
                'value' => 'Lewis',
            ],
        ]);

        $this->assertEquals(2, count($authors), 'There must be 2 authors named Lewis');
    }

    /**
     * @expectedException \SAF\SearchableRepository\Exception\FieldOrAssociationNotFoundException
     */
    public function testFieldOrAssociationNotFoundException()
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
