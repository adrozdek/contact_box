<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Email;
use ContactBoxBundle\Entity\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/addressBook")
 */
class EmailController extends Controller
{
    public function generateEmailForm($email, $action)
    {
        $form = $this->createFormBuilder($email);
        $form->add('email', 'text', array('trim' => true, 'required' => false));
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
     * @Route("/{id}/addEmail", name = "showNewEmail")
     * @Template("ContactBoxBundle:Email:newEmail.html.twig")
     * @Method("GET")
     */
    public function newEmailAction($id)
    {
        $email = new Email();
        $emailForm = $this->generateEmailForm($email, $this->generateUrl('newEmail', ['id' => $id]));

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        return ['formEmail' => $emailForm->createView(), 'person' => $person];
    }

    /**
     * @Route("/{id}/addEmail", name = "newEmail")
     * @Template("ContactBoxBundle:Email:newEmail.html.twig")
     * @Method("POST")
     */
    public function newEmailPostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        $email = new Email();

        $form = $this->generateEmailForm($email, $this->generateUrl('newEmail', ['id' => $id]));
        $form->handleRequest($req);

        //WALIDACJA DLUGA OPCJA:
//        $validator = $this->get('validator'); //pobieramy walidator
//        $errors = $validator->validate($email);  //sprawdzamy czy walidacje spełnione
//
//        if (count($errors) > 0) {    //jeśli błędów więcej niż 0 to:
//            return $this->render('ContactBoxBundle:Person:error.html.twig', ['errors' => $errors]);
//        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $email->setPerson($person);
            $person->addEmail($email);

            $em->persist($email);

            $em->flush();

            return $this->redirectToRoute('showOne', ['id' => $id]); //jeśli sie dodało to się dodało i wyświetl

        } else {
            return $this->render('ContactBoxBundle:Email:newEmail.html.twig', ['formEmail' => $form->createView(), 'person' => $person]);
            //nie robimy redirecta
        }



    }

    /**
     * @Route("/{id}/modifyEmail", name = "editEmail")
     * @Template("ContactBoxBundle:Email:newEmail.html.twig")
     * @Method("GET")
     */
    public function modifyEmailAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Email');
        $email = $repo->find($id);

        $person = $email->getPerson();

        $emailForm = $this->generateEmailForm($email, $this->generateUrl('editEmailSavePerson', ['id' => $id]));

        return ['formEmail' => $emailForm->createView(), 'person' => $person];
    }

    /**
     * @Route("/{id}/modifyEmail", name = "editEmailSavePerson")
     * @Template("ContactBoxBundle:Email:newEmail.html.twig")
     * @Method("POST")
     */
    public function modifyEmailPostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Email');
        $email = $repo->find($id);

        $emailForm = $this->generateEmailForm($email, $this->generateUrl('editEmailSavePerson', ['id' => $id]));

        $emailForm->handleRequest($req);

        if ($emailForm->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();


            $em->flush();
        }

        $personId = $email->getPerson()->getId();
        return $this->redirectToRoute('showOne', ['id' => $personId]);

    }

    /**
     * @Route("/{id}/removeEmail", name = "removeEmail")
     */
    public function removeEmailAction($id)
    {

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Email');

        $email = $repo->find($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($email);

        $em->flush();

        $personId = $email->getPerson()->getId();
        return $this->redirectToRoute('showOne', ['id' => $personId]);
    }


}
