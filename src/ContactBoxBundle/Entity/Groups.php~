<?php

namespace ContactBoxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContactBoxBundle\Entity\GroupsRepository")
 */
class Groups
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
     * @ORM\Column(name="name", type="string", length=80)
     */
    private $name;


    /**
     * @ORM\ManyToMany( targetEntity = "Person", mappedBy = "groups")
     */
    private $persons;

    /**
     * @ORM\ManyToOne( targetEntity = "User", inversedBy = "contact_groups" )
     * @ORM\JoinColumn( name = "user_owner_id", referencedColumnName = "id")
     *
     */
    private $userOwner;


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
     * Set name
     *
     * @param string $name
     * @return Groups
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->persons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add persons
     *
     * @param \ContactBoxBundle\Entity\Person $persons
     * @return Groups
     */
    public function addPerson(\ContactBoxBundle\Entity\Person $persons)
    {
        $this->persons[] = $persons;

        return $this;
    }

    /**
     * Remove persons
     *
     * @param \ContactBoxBundle\Entity\Person $persons
     */
    public function removePerson(\ContactBoxBundle\Entity\Person $persons)
    {
        $this->persons->removeElement($persons);
    }

    /**
     * Get persons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * Set user
     *
     * @param \ContactBoxBundle\Entity\User $user
     * @return Groups
     */
    public function setUser(\ContactBoxBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ContactBoxBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set userOwner
     *
     * @param \ContactBoxBundle\Entity\User $userOwner
     * @return Groups
     */
    public function setUserOwner(\ContactBoxBundle\Entity\User $userOwner = null)
    {
        $this->userOwner = $userOwner;

        return $this;
    }

    /**
     * Get userOwner
     *
     * @return \ContactBoxBundle\Entity\User 
     */
    public function getUserOwner()
    {
        return $this->userOwner;
    }
}
