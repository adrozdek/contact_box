<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Email;
use ContactBoxBundle\Entity\Person;
use ContactBoxBundle\Entity\Address;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;


class PersonController extends Controller
{
    private function generatePersonForm($person, $action)
    {
        $form = $this->createFormBuilder($person);
        $form->add('firstName', 'text', array('trim' => true));
        $form->add('lastName', 'text', array('trim' => true));
        $form->add('description', 'textarea', array('trim' => true));
        $form->add('groups', 'entity', array(
            'class' => 'ContactBoxBundle:Groups',
            'choice_label' => 'name',
            'expanded' => 'true',
            'multiple' => 'true',
        ));
        $form->add('photoPath', 'file', array('data_class' => null,
            'required' => false
        ));
        $form->add('save', 'submit');
        $form->setAction($action);

        $personForm = $form->getForm();

        return $personForm;

    }

    /**
     * @Route("/new", name="new")
     * @Template("ContactBoxBundle:Person:new.html.twig")
     * @Method("GET")
     */
    public function newAction()
    {
        $person = new Person();
        $personForm = $this->generatePersonForm($person, $this->generateUrl('newPerson'));

        return ['form' => $personForm->createView()];
    }

    /**
     * @Route("/new", name = "newPerson")
     * @Template("ContactBoxBundle:Person:new.html.twig")
     * @Method("POST")
     */
    public function newPostAction(Request $req)
    {
        $person = new Person();

        $form = $this->generatePersonForm($person, $this->generateUrl('newPerson'));
        $form->handleRequest($req);


        if ($person->getPhotoPath() != null) {
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $person->getPhotoPath();

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $photoDir = $this->container->getParameter('kernel.root_dir') . '/../web/photos';
            $file->move($photoDir, $fileName);

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $person->setPhotoPath($fileName);
        }
        // ... persist the $product variable or any other work


        $em = $this->getDoctrine()->getManager();

        $em->persist($person);

        $em->flush();

        $id = $person->getId();

        return $this->redirectToRoute('showOne', ['id' => $id]);
    }

    /**
     * @Route("/{id}/modify", name = "editPerson")
     * @Template()
     * @Method("GET")
     */
    public function modifyAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);
        //$photoPath = '../web/photos/' . $person->getPhotoPath();

        $personForm = $this->generatePersonForm($person, $this->generateUrl('editSavePerson', ['id' => $id]));

        return ['form' => $personForm->createView(), 'person' => $person];
    }

    /**
     * @Route("/{id}/modify", name = "editSavePerson")
     * @Template()
     * @Method("POST")
     */
    public function modifyPostAction(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        $oldPath = $person->getPhotoPath();


        $personForm = $this->generatePersonForm($person, $this->generateUrl('editSavePerson', ['id' => $id]));

        $personForm->handleRequest($req);

        if ($person->getPhotoPath() != null) {

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $person->getPhotoPath();

            if ($file != null) {
                if ($oldPath != null) {
                    $path = $this->container->getParameter('kernel.root_dir') . '/../web/photos/' . $oldPath;
                    unlink($path);
                }
            }

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $photoDir = $this->container->getParameter('kernel.root_dir') . '/../web/photos';
            $file->move($photoDir, $fileName);

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $person->setPhotoPath($fileName);
        } else {
            $person->setPhotoPath($oldPath);
        }

        if ($personForm->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('showOne', ['id' => $person->getId()]);

    }


    /**
     * @Route("/{id}/delete", name = "deletePerson")
     *
     */
    public
    function deleteAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');

        $person = $repo->find($id);

        $photoPath = $person->getPhotoPath();

        if ($photoPath != null) {
            $path = $this->container->getParameter('kernel.root_dir') . '/../web/photos/' . $photoPath;
            unlink($path);
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($person);

        $em->flush();

        return $this->redirectToRoute('showAll');

    }

    /**
     * @Route("/{id}", name = "showOne")
     * @Template()
     */
    public function showOneAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);

        return ['person' => $person];
    }

    /**
     * @Route("/", name = "showAll")
     * @Template()
     */
    public function showAllAction()
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $persons = $repo->findOrderedByLastName();

        return ['persons' => $persons];
    }



    /*
        private function generateGroupsForm($person, $action)
        {
            $form = $this->createFormBuilder($person);
            $form->add('groups', 'entity', array(

                'class' => 'ContactBoxBundle:Groups',
                'choice_label' => 'name',
                'expanded' => 'true',
                'multiple' => 'true',

            ));
            $form->add('save', 'submit');
            $form->setAction($action);

            $groupsForm = $form->getForm();

            return $groupsForm;
        }

        private function generateSearchAction($person, $search) {
            $form = $this->createFormBuilder($person);
            $form->add('');
        }

        /**
         * @Route("/{id}/editGroups", name = "editGroups")
         * @Template("ContactBoxBundle:Groups:editGroups.html.twig")
         * @Method("GET")

        public function editGroupsAction($id)
        {
            $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
            $person = $repo->find($id);

            $groupsForm = $this->generateGroupsForm($person, $this->generateUrl('editGroupsPost', ['id' => $id]));

            return ['formGroups' => $groupsForm->createView(), 'person' => $person];

        }

    */
//    /**
//     * @Route("/{id}/editGroups", name = "editGroupsPost")
//     * @Template("ContactBoxBundle:Groups:editGroups.html.twig")
//     * @Method("POST")
//     */
//    public function editGroupsPostAction(Request $req, $id)
//    {
//        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
//        $person = $repo->find($id);
//
//        $groupsForm = $this->generateGroupsForm($person, $this->generateUrl('editGroupsPost', ['id' => $id]));
//
//        $groupsForm->handleRequest($req);
//
//        if ($groupsForm->isSubmitted()) {
//            $em = $this->getDoctrine()->getManager();
//
//
//            $em->flush();
//        }
//
//
//        return $this->redirectToRoute('showOne', ['id' => $id]);
//
//    }







}
