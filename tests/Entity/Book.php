<?php

/*
 * This file is part of the doctrine-orm-searchable-repository project.
 *
 * (c) Vincent Touzet <vincent.touzet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\SAF\SearchableRepository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Book.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tests\SAF\SearchableRepository\Entity\Repository\PostRepository")
 */
class Book
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $publishedOn;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @var Author
     *
     * @ORM\ManyToOne(targetEntity="Author")
     */
    protected $author;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $nbSales;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedOn()
    {
        return $this->publishedOn;
    }

    /**
     * @param \DateTime $publishedOn
     */
    public function setPublishedOn($publishedOn)
    {
        $this->publishedOn = $publishedOn;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return int
     */
    public function getNbSales()
    {
        return $this->nbSales;
    }

    /**
     * @param int $nbSales
     */
    public function setNbSales($nbSales)
    {
        $this->nbSales = $nbSales;
    }
}
