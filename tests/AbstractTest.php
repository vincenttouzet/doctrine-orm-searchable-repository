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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Nelmio\Alice\Loader\NativeLoader;
use SAF\SearchableRepository\SearchableRepository;
use Tests\SAF\SearchableRepository\Entity\Author;

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

    protected function setUp()
    {
        // create config - Do not use simple annotation reader
        $useSimpleAnnotationReader = false;
        $config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/Entity'], true, null, null, $useSimpleAnnotationReader);

        $conn = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];

        $this->entityManager = EntityManager::create($conn, $config);
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($metadatas);

        // loader fixtures for test
        $loader = new NativeLoader();
        $objectSet = $loader->loadFile(__DIR__.'/fixtures.yml');

        foreach ($objectSet->getObjects() as $object) {
            $this->entityManager->persist($object);
        }

        $this->entityManager->flush();
    }

    /**
     * @param $class
     * @return SearchableRepository
     */
    protected function getEntityRepository($class)
    {
        return $this->entityManager->getRepository($class);
    }
}
