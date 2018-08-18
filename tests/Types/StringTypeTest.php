<?php

/*
 * This file is part of the doctrine-orm-searchable-repository project.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\SAF\SearchableRepository\Types;

use SAF\SearchableRepository\Filter;
use Tests\SAF\SearchableRepository\AbstractTest;
use Tests\SAF\SearchableRepository\Entity\Author;
use Tests\SAF\SearchableRepository\Entity\Book;
use Tests\SAF\SearchableRepository\Entity\Type;

class StringTypeTest extends AbstractTest
{
    public function testLikeCondition()
    {
        $repository = $this->getEntityRepository(Author::class);

        $authors = $repository->search([
            'firstName' => [Filter::CONDITION_LIKE => 'Lew%'],
            'lastName' => [Filter::CONDITION_LIKE => 'Car%ll'],
        ]);

        $this->assertEquals(1, count($authors), 'There must be 1 author named "Lewis Carroll"');
    }

    public function testNotLikeCondition()
    {
        $repository = $this->getEntityRepository(Author::class);

        $authors = $repository->search([
            'lastName' => [Filter::CONDITION_NOT_LIKE => 'Car%ll'],
        ]);

        $this->assertEquals(10, count($authors), 'There must be 10 author not named "Lewis Carroll"');
    }

    public function testOrderByASC()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([], [
            'name' => 'ASC',
        ]);

        $this->assertEquals('Biography', $types[0]->getName(), 'First type must be Biography');
        $this->assertEquals('Documentary', $types[1]->getName(), 'Second type must be Documentary');
        $this->assertEquals('Novel', $types[2]->getName(), 'Third type must be Novel');
    }

    public function testOrderByDESC()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([], [
            'name' => 'DESC',
        ]);

        $this->assertEquals('Novel', $types[0]->getName(), 'First type must be Novel');
        $this->assertEquals('Documentary', $types[1]->getName(), 'Second type must be Documentary');
        $this->assertEquals('Biography', $types[2]->getName(), 'Third type must be Biography');
    }

    /**
     * @expectedException \Saf\SearchableRepository\Exception\ConditionNotSupportedException
     */
    public function testConditionNotSupportedException()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => ['unknown_condition' => true],
        ]);
    }
}
