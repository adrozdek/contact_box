<?php
// src/ContactBoxBundle/Entity/User.php

namespace ContactBoxBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany( targetEntity = "Person", inversedBy = "users" )
     * @ORM\JoinTable( name = "user_person" )
     */
    protected $persons;

    /**
     * @ORM\OneToMany( targetEntity = "Groups", mappedBy = "userOwner" )
     */
    protected $contact_groups;

    /**
     * @ORM\OneToMany( targetEntity = "Person", mappedBy = "userOwner" )
     */
    protected $ownedPersons;



    /**
     * Add persons
     *
     * @param \ContactBoxBundle\Entity\Person $persons
     * @return User
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
     * Add ownedPersons
     *
     * @param \ContactBoxBundle\Entity\Person $ownedPersons
     * @return User
     */
    public function addOwnedPerson(\ContactBoxBundle\Entity\Person $ownedPersons)
    {
        $this->ownedPersons[] = $ownedPersons;

        return $this;
    }

    /**
     * Remove ownedPersons
     *
     * @param \ContactBoxBundle\Entity\Person $ownedPersons
     */
    public function removeOwnedPerson(\ContactBoxBundle\Entity\Person $ownedPersons)
    {
        $this->ownedPersons->removeElement($ownedPersons);
    }

    /**
     * Get ownedPersons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnedPersons()
    {
        return $this->ownedPersons;
    }

    /**
     * Add contact_groups
     *
     * @param \ContactBoxBundle\Entity\Groups $contactGroups
     * @return User
     */
    public function addContactGroup(\ContactBoxBundle\Entity\Groups $contactGroups)
    {
        $this->contact_groups[] = $contactGroups;

        return $this;
    }

    /**
     * Remove contact_groups
     *
     * @param \ContactBoxBundle\Entity\Groups $contactGroups
     */
    public function removeContactGroup(\ContactBoxBundle\Entity\Groups $contactGroups)
    {
        $this->contact_groups->removeElement($contactGroups);
    }

    /**
     * Get contact_groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactGroups()
    {
        return $this->contact_groups;
    }
}
