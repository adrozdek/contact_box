<?php

namespace ContactBoxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Phone
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContactBoxBundle\Entity\PhoneRepository")
 */
class Phone
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
     * @var integer
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @ORM\ManyToOne( targetEntity = "Type", inversedBy = "phones" )
     * @ORM\JoinColumn( name = "type_id", referencedColumnName = "id")
     */
    private $type;

    /**
     * @ORM\ManyToOne( targetEntity = "Person", inversedBy = "phones" )
     * @ORM\JoinColumn( name = "person_id", referencedColumnName = "id", onDelete="CASCADE")
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
     * Set number
     *
     * @param integer $number
     * @return Phone
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set person
     *
     * @param \ContactBoxBundle\Entity\Person $person
     * @return Phone
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

    /**
     * Set type
     *
     * @param \ContactBoxBundle\Entity\Type $type
     * @return Phone
     */
    public function setType(\ContactBoxBundle\Entity\Type $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \ContactBoxBundle\Entity\Type 
     */
    public function getType()
    {
        return $this->type;
    }
}
