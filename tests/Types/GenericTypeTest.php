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

class GenericTypeTest extends AbstractTest
{
    public function testEqCondition()
    {
        $repository = $this->getEntityRepository(Author::class);

        $authors = $repository->search([
            'firstName' => [Filter::CONDITION_EQ => 'Lewis'],
            'lastName' => [Filter::CONDITION_EQ => 'Carroll'],
        ]);

        $this->assertEquals(1, count($authors), 'There must be 1 author named "Lewis Carroll"');
    }

    public function testNeqCondition()
    {
        $repository = $this->getEntityRepository(Author::class);

        $authors = $repository->search([
            'firstName' => [Filter::CONDITION_NEQ => 'Lewis'],
            'lastName' => [Filter::CONDITION_NEQ => 'Carroll'],
        ]);

        $this->assertEquals(10, count($authors), 'There must be 10 author not named "Lewis Carroll"');
    }

    public function testLikeCondition()
    {
        $repository = $this->getEntityRepository(Author::class);

        $authors = $repository->search([
            'birthDate' => [Filter::CONDITION_LIKE => '1832%'],
        ]);

        $this->assertEquals(1, count($authors), 'There must be 1 author birth in 1832');
    }

    public function testNotLikeCondition()
    {
        $repository = $this->getEntityRepository(Author::class);

        $authors = $repository->search([
            'birthDate' => [Filter::CONDITION_NOT_LIKE => '1832%'],
        ]);

        $this->assertEquals(10, count($authors), 'There must be 10 author not birth on 1832');
    }

    public function testContains()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([
            'name' => [Filter::CONDITION_CONTAINS => 'e'],
        ]);

        $this->assertEquals(2, count($types), 'There must be 2 types with an "e" in their name');
    }

    public function testNotContains()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([
            'name' => [Filter::CONDITION_NOT_CONTAINS => 'a'],
        ]);

        $this->assertEquals(1, count($types), 'There must be 1 type with no "a" in their name');
    }

    public function testStarsWith()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([
            'name' => [Filter::CONDITION_STARTS_WITH => 'No'],
        ]);

        $this->assertEquals(1, count($types), 'There must be 1 type starting with No');
    }

    public function testNotStarsWith()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([
            'name' => [Filter::CONDITION_NOT_STARTS_WITH => 'No'],
        ]);

        $this->assertEquals(2, count($types), 'There must be 2 type not starting with No');
    }

    public function testEndsWith()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([
            'name' => [Filter::CONDITION_ENDS_WITH => 'phy'],
        ]);

        $this->assertEquals(1, count($types), 'There must be 1 type starting with No');
    }

    public function testNotEndsWith()
    {
        $repository = $this->getEntityRepository(Type::class);

        $types = $repository->search([
            'name' => [Filter::CONDITION_NOT_ENDS_WITH => 'phy'],
        ]);

        $this->assertEquals(2, count($types), 'There must be 2 type not starting with No');
    }

    public function testLtCondition()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_LT => 50],
        ]);

        $this->assertEquals(9, count($books), 'There must be 9 books with less than 50 sales');
    }

    public function testLteCondition()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_LTE => 50],
        ]);

        $this->assertEquals(10, count($books), 'There must be 10 books with less than or 50 sales');
    }

    public function testGtCondition()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_GT => 50],
        ]);

        $this->assertEquals(10, count($books), 'There must be 10 books with more than 50 sales');
    }

    public function testGteCondition()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_GTE => 50],
        ]);

        $this->assertEquals(11, count($books), 'There must be 11 books with more than or 50 sales');
    }

    public function testBetweenCondition()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_BETWEEN => [25, 49]],
        ]);

        $this->assertEquals(4, count($books), 'There must be 4 books with nb sales between 25 and 49');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBetweenConditionExceptionType()
    {
        $repository = $this->getEntityRepository(Book::class);

        $repository->search([
            'nbSales' => [Filter::CONDITION_BETWEEN => 25],
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBetweenConditionExceptionNotEnough()
    {
        $repository = $this->getEntityRepository(Book::class);

        $repository->search([
            'nbSales' => [Filter::CONDITION_BETWEEN => [25]],
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBetweenConditionExceptionTooMuch()
    {
        $repository = $this->getEntityRepository(Book::class);

        $repository->search([
            'nbSales' => [Filter::CONDITION_BETWEEN => [15, 20, 25]],
        ]);
    }

    public function testNullCondition()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_NULL => true],
        ]);

        $this->assertEquals(10, count($books), 'There must be 10 books with sales set to null');

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_NULL => false],
        ]);

        $this->assertEquals(20, count($books), 'There must be 20 books with sales not set to null');
    }

    public function testNotNullCondition()
    {
        $repository = $this->getEntityRepository(Book::class);

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_NOT_NULL => true],
        ]);

        $this->assertEquals(20, count($books), 'There must be 20 books with sales not set to null');

        $books = $repository->search([
            'nbSales' => [Filter::CONDITION_NOT_NULL => false],
        ]);

        $this->assertEquals(10, count($books), 'There must be 10 books with sales set to null');
    }

    public function testInCondition()
    {
        $repository = $this->getEntityRepository(Type::class);

        $typeNames = ['Novel', 'Biography'];
        $types = $repository->search([
            'name' => [Filter::CONDITION_IN => $typeNames],
        ]);

        $this->assertEquals(2, count($types), sprintf('There must be 2 types in [%s]', implode(', ', $typeNames)));
    }

    public function testNotInCondition()
    {
        $repository = $this->getEntityRepository(Type::class);

        $typeNames = ['Novel', 'Biography'];
        $types = $repository->search([
            'name' => [Filter::CONDITION_NOT_IN => $typeNames],
        ]);

        $this->assertEquals(1, count($types), sprintf('There must be 1 types not in [%s]', implode(', ', $typeNames)));
    }

    public function testOrderByASC()
    {
        $repository = $this->getEntityRepository(Book::class);
        /** @var Book[] $books */
        $books = $repository->search([], ['nbSales' => 'ASC']);
        $last = PHP_INT_MIN;
        foreach ($books as $book) {
            if ($book->getNbSales()) {
                $this->assertGreaterThanOrEqual($last, $book->getNbSales());
            }
        }
    }

    public function testOrderByDESC()
    {
        $repository = $this->getEntityRepository(Book::class);
        /** @var Book[] $books */
        $books = $repository->search([], ['nbSales' => 'ASC']);
        $last = PHP_INT_MAX;
        foreach ($books as $book) {
            if ($book->getNbSales()) {
                $this->assertLessThanOrEqual($last, $book->getNbSales());
            }
        }
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
