<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Phone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/addressBook")
 */
class PhoneController extends Controller
{
    
    public function generatePhoneForm($Phone, $action)
    {
        $form = $this->createFormBuilder($Phone);
        $form->add('number', 'text', array('trim' => true));
        $form->add('type', 'entity', array(
            'class' => 'ContactBoxBundle:Type',
            'choice_label' => 'name',
            'expanded' => 'true',
        ));
        $form->add('save', 'submit');
        $form->setAction($action);

        $personForm = $form->getForm();

        return $personForm;
    }

    /**
     * @Route("/{id}/addPhone", name = "showNewPhone")
     * @Template("ContactBoxBundle:Phone:newPhone.html.twig")
     * @Method("GET")
     */
    public function newPhoneAction($id)
    {
        $Phone = new Phone();
        $PhoneForm = $this->generatePhoneForm($Phone, $this->generateUrl('newPhone', ['id' => $id]));

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        return ['formPhone' => $PhoneForm->createView(), 'person' => $person];
    }

    /**
     * @Route("/{id}/addPhone", name = "newPhone")
     * @Template("ContactBoxBundle:Phone:newPhone.html.twig")
     * @Method("POST")
     */
    public function newPhonePostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        $phone = new Phone();

        $form = $this->generatePhoneForm($phone, $this->generateUrl('newPhone', ['id' => $id]));
        $form->handleRequest($req);

        $em = $this->getDoctrine()->getManager();

        $phone->setPerson($person);
        $person->addPhone($phone);

        $em->persist($phone);

        $em->flush();


        return $this->redirectToRoute('showOne', ['id' => $id]);
    }

    /**
     * @Route("/{id}/modifyPhone", name = "editPhone")
     * @Template("ContactBoxBundle:Phone:newPhone.html.twig")
     * @Method("GET")
     */
    public function modifyPhoneAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Phone');
        $phone = $repo->find($id);

        $person = $phone->getPerson();

        $phoneForm = $this->generatePhoneForm($phone, $this->generateUrl('editPhoneSavePerson', ['id' => $id]));

        return ['formPhone' => $phoneForm->createView(), 'person' => $person];
    }

    /**
     * @Route("/{id}/modifyPhone", name = "editPhoneSavePerson")
     * @Template("ContactBoxBundle:Phone:newPhone.html.twig")
     * @Method("POST")
     */
    public function modifyPhonePostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Phone');
        $phone = $repo->find($id);

        $phoneForm = $this->generatePhoneForm($phone, $this->generateUrl('editPhoneSavePerson', ['id' => $id]));

        $phoneForm->handleRequest($req);

        if ($phoneForm->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();


            $em->flush();


        }
        $personId = $phone->getPerson()->getId();

        return $this->redirectToRoute('showOne', ['id' => $personId]);

    }

    /**
     * @Route("/{id}/removePhone", name = "removePhone")
     */
    public function removePhoneAction($id)
    {

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Phone');

        $phone = $repo->find($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($phone);

        $em->flush();

        $personId = $phone->getPerson()->getId();
        return $this->redirectToRoute('showOne', ['id' => $personId]);
    }


}



