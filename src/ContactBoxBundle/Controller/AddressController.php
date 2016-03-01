<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/addressBook")
 */
class AddressController extends Controller
{

    public function generateAddressForm($address, $action)
    {
        $form = $this->createFormBuilder($address);
        $form->add('city', 'text', array('label' => 'Miasto', 'trim' => true));
        $form->add('street', 'text', array('label' => 'Ulica: ', 'trim' => true));
        $form->add('homeNumber', 'text', array('label' => 'Number domu: ', 'trim' => true));
        $form->add('apartmentNumber', 'number', array('required' => false, 'trim' => true));
        $form->add('save', 'submit');
        $form->setAction($action);

        $personForm = $form->getForm();

        return $personForm;
    }

    /**
     * @Route("/{id}/addAddress", name = "showNewAddress")
     * @Template("ContactBoxBundle:Address:newAddress.html.twig")
     * @Method("GET")
     */
    public function newAddressAction($id)
    {
        $address = new Address();
        $addressForm = $this->generateAddressForm($address, $this->generateUrl('newAddress', ['id' => $id]));


        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        return ['formAddress' => $addressForm->createView(), 'person' => $person];
    }

    /**
     * @Route("/{id}/addAddress", name = "newAddress")
     * @Template("ContactBoxBundle:Address:newAddress.html.twig")
     * @Method("POST")
     */
    public function newAddressPostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        $address = new Address();

        $form = $this->generateAddressForm($address, $this->generateUrl('newAddress', ['id' => $id]));
        $form->handleRequest($req);

        $em = $this->getDoctrine()->getManager();

        $address->setPerson($person);
        $person->addAddress($address);

        $em->persist($address);

        $em->flush();


        return $this->redirectToRoute('showOne', ['id' => $id]);
    }

    /**
     * @Route("/{id}/modifyAddress", name = "editAddress")
     * @Template("ContactBoxBundle:Address:newAddress.html.twig")
     * @Method("GET")
     */
    public function modifyAddressAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Address');
        $address = $repo->find($id);

        $person = $address->getPerson();

        $addressForm = $this->generateAddressForm($address, $this->generateUrl('editAddressSavePerson', ['id' => $id]));

        return ['formAddress' => $addressForm->createView(), 'person' => $person];
    }

    /**
     * @Route("/{id}/modifyAddress", name = "editAddressSavePerson")
     * @Template("ContactBoxBundle:Address:newAddress.html.twig")
     * @Method("POST")
     */
    public function modifyAddressPostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Address');
        $address = $repo->find($id);

        $addressForm = $this->generateAddressForm($address, $this->generateUrl('editAddressSavePerson', ['id' => $id]));

        $addressForm->handleRequest($req);

        if ($addressForm->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();


            $em->flush();
        }
        $personId = $address->getPerson()->getId();

        return $this->redirectToRoute('showOne', ['id' => $personId]);

    }

    /**
     * @Route("/{id}/removeAddress", name = "removeAddress")
     */
    public function removeAddressAction($id)
    {

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Address');

        $address = $repo->find($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($address);

        $em->flush();

        $personId = $address->getPerson()->getId();
        return $this->redirectToRoute('showOne', ['id' => $personId]);
    }

}
