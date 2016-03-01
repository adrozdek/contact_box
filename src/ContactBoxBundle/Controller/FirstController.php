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
        return $this->redirectToRoute('fos_user_security_login');
    }

}
