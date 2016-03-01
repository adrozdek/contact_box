<?php

namespace ContactBoxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class FirstController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function redirectToLoginAction()
    {
        if ($this->getUser() != null)
            return $this->redirectToRoute('showAll');
        else {
            return $this->redirectToRoute('fos_user_security_login');
        }

        //@TODO: jeśli zalogowany user to ma przekierowywać na showAll
    }


}
