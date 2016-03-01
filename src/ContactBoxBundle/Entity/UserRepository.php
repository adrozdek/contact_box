<?php

namespace ContactBoxBundle\Entity;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
    public function findByEmail($email) {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT user FROM ContactBoxBundle:User user WHERE user.email = :email'
        );
        $query->setParameter('email', $email);
        return $query->getResult();
    }




}



