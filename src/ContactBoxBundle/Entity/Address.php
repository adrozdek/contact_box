<?php

namespace ContactBoxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContactBoxBundle\Entity\AddressRepository")
 */
class Address
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
     * @ORM\Column(name="city", type="string", length=80)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=70)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="homeNumber", type="string")
     */
    private $homeNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="apartmentNumber", type="integer", nullable = true)
     */
    private $apartmentNumber;

    /**
     * @ORM\ManyToOne( targetEntity = "Person", inversedBy = "addresses" )
     * @ORM\JoinColumn( name = "person_id", referencedColumnName = "id")
     */
    private $person;


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
     * Set city
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Address
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set homeNumber
     *
     * @param integer $homeNumber
     * @return Address
     */
    public function setHomeNumber($homeNumber)
    {
        $this->homeNumber = $homeNumber;

        return $this;
    }

    /**
     * Get homeNumber
     *
     * @return integer 
     */
    public function getHomeNumber()
    {
        return $this->homeNumber;
    }

    /**
     * Set apartmentNumber
     *
     * @param integer $apartmentNumber
     * @return Address
     */
    public function setApartmentNumber($apartmentNumber)
    {
        $this->apartmentNumber = $apartmentNumber;

        return $this;
    }

    /**
     * Get apartmentNumber
     *
     * @return integer 
     */
    public function getApartmentNumber()
    {
        return $this->apartmentNumber;
    }



    /**
     * Set person
     *
     * @param \ContactBoxBundle\Entity\Person $person
     * @return Address
     */
    public function setPerson(\ContactBoxBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \ContactBoxBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }
}
