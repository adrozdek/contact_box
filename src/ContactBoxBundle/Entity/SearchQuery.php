<?php

namespace ContactBoxBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class SearchQuery
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="string", message="form.search.query.validation.type")
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "form.search.query.validation.min",
     *      maxMessage = "form.search.query.validation.max"
     * )
     */
    protected $query;

    public function __construct($query = '')
    {
        $this->setQuery($query);
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }
}
