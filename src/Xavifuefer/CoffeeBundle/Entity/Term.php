<?php

namespace Xavifuefer\CoffeeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Bean
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Term
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="query", type="string", length=255)
     */
    private $query;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="Xavifuefer\CoffeeBundle\Entity\Bean", mappedBy="term", cascade={"persist", "remove"})
     */
    private $beans;

    public function __construct()
    {
        $this->beans = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set query
     *
     * @param string $query
     * @return Term
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add beans
     *
     * @param \Xavifuefer\CoffeeBundleEntity\Bean $beans
     * @return Pot
     */
    public function addBean(\Xavifuefer\CoffeeBundle\Entity\Bean $bean)
    {
        $bean->setTerm($this);
        $this->beans[] = $bean;

        return $this;
    }

    /**
     * Remove beans
     *
     * @param \Xavifuefer\CoffeeBundle\Entity\Bean $beans
     */
    public function removeBean(\Xavifuefer\CoffeeBundle\Entity\Bean $bean)
    {
        $this->beans->removeElement($bean);
    }

    /**
     * Get beans
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBeans()
    {
        return $this->beans;
    }
}
