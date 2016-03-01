<?php

namespace ContactBoxBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Email
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ContactBoxBundle\Entity\EmailRepository")
 */
class Email
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=90)
     * @Assert\NotBlank(message = "Podaj email")
     * @Assert\Email(message = "Podaj prawidłowy adres email")
     */
    private $email;

    /**
     * @ORM\ManyToOne( targetEntity = "Type", inversedBy = "emails" )
     * @ORM\JoinColumn( name = "type_id", referencedColumnName = "id")
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\ManyToOne( targetEntity = "Person", inversedBy = "emails" )
     * @ORM\JoinColumn( name = "person_id", referencedColumnName = "id", onDelete="CASCADE")
     *
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
     * Set email
     *
     * @param string $email
     * @return Email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set person
     *
     * @param \ContactBoxBundle\Entity\Person $person
     * @return Email
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
     * @return Email
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
