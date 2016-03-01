<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Email;
use ContactBoxBundle\Entity\GroupsRepository;
use ContactBoxBundle\Entity\Person;
use ContactBoxBundle\Entity\Address;

use Doctrine\ORM\EntityRepository;
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

/**
 * @Route("/addressBook")
 */
class PersonController extends Controller
{
    private function generatePersonForm($person, $action, $user)
    {
        $form = $this->createFormBuilder($person);
        $form->add('firstName', 'text', array('trim' => true));
        $form->add('lastName', 'text', array('trim' => true));
        $form->add('description', 'textarea', array('trim' => true));
        $form->add('groups', 'entity', array(
            'class' => 'ContactBoxBundle\Entity\Groups',
            'property' => 'name',
            'query_builder' => function (GroupsRepository $er) use ($user) {
                return $er->createQueryBuilder('w')
                    ->orderBy('w.name', 'ASC')
                    ->where('w.userOwner = ?1')
                    ->setParameter(1, $user);
            },
            'required' => false,
            'multiple' => true,
            'expanded' => true
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
        $user = $this->getUser();
        $personForm = $this->generatePersonForm($person, $this->generateUrl('newPerson'), $user);

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
        $user = $this->getUser();

        $form = $this->generatePersonForm($person, $this->generateUrl('newPerson'), $user);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

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

            $user = $this->getUser();

            $person->setUserOwner($user);
            $user->addPerson($person);

            $em = $this->getDoctrine()->getManager();

            $em->persist($person);

            $em->flush();

            $id = $person->getId();

            return $this->redirectToRoute('showOne', ['id' => $id]);
        } else {
            return $this->redirectToRoute('showAll');
        }
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
        $user = $this->getUser();
        if ($person->getUserOwner()->getId() != $user->getId()) {
            return $this->redirectToRoute('showAll');
        } else {

            $personForm = $this->generatePersonForm($person, $this->generateUrl('editSavePerson', ['id' => $id]), $user);

            return ['form' => $personForm->createView(), 'person' => $person];
        }
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
        $user = $this->getUser();

        if ($person->getUserOwner()->getId() != $user->getId()) {
            return $this->redirectToRoute('showAll');

        } else {
            $oldPath = $person->getPhotoPath();

            $personForm = $this->generatePersonForm($person, $this->generateUrl('editSavePerson', ['id' => $id]), $user);

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

    }


    /**
     * @Route("/{id}/delete", name = "deletePerson")
     */
    public
    function deleteAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');

        $person = $repo->find($id);
        $user = $this->getUser();

        //jeśli nie jesteś właścicielem kontaktu
        if ($person->getUserOwner()->getId() != $user->getId()) {
            //ale, jeśli kontakt zawiera Cię w użytkownikach to:
            if($person->getUsers()->contains($user)) {
                //usuń go z moich kontaktów
                $user->removePerson($person);
                $person->removeUser($user);

                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }
        } else {
            $photoPath = $person->getPhotoPath();

            if ($photoPath != null) {
                $path = $this->container->getParameter('kernel.root_dir') . '/../web/photos/' . $photoPath;
                unlink($path);
            }

            $em = $this->getDoctrine()->getManager();

            $em->remove($person);

            $em->flush();
        }
        return $this->redirectToRoute('showAll');
    }

    /**
     * @Route("/person/{id}", name = "showOne")
     * @Template()
     */
    public function showOneAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $repo->find($id);
        $user = $this->getUser();

        $usersWithAccess = $person->getUsers();

        //sprawdzenie czy obecny user ma dany kontakt w swoich lub udostępnionych mu kontaktach:
        if($usersWithAccess->contains($user)) {
            return ['person' => $person];

        } else {
            return $this->redirectToRoute('showAll');
        }

    }

    /**
     * @Route("/", name = "showAll")
     * @Template()
     */
    public function showAllAction()
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');

        //findByUser znajduje nam wszystkie kontakty danego usera - userOwner oraz udostępnione dla niego:
        $persons = $repo->findByUser($this->getUser());

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


    /**
     * @Route("/search", name = "search")
     * @Method("GET")
     *
     */
    public function formSearchAction()
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $allPeople = $repo->findByUser($this->getUser());

        return $this->render('ContactBoxBundle:Person:showAll.html.twig', ['persons' => $allPeople]);
    }

    /**
     * @Route("/search", name = "searchPost")
     * @Method("POST")
     *
     */
    public function formSearchPostAction(Request $req)
    {
        $name = trim($req->request->get('name'));

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $people = $repo->searchByLastName($this->getUser(), $name);


        return $this->render('ContactBoxBundle:Person:showAll.html.twig', array('persons' => $people, 'searchName' => $name));

    }

    /**
     * @Route("/share/{id}", name = "share")
     * @Method("GET")
     * @Template("ContactBoxBundle:Person:shareContact.html.twig")
     */
    public function shareContactAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $allPeople = $repo->findByUser($this->getUser());
        $personToShare = $repo->find($id);
        $user = $this->getUser();

//        if( $personToShare->getUserOwner()->getId() != $user->getId() ) {
        if(!($personToShare->getUsers()->contains($user))) {
            return $this->redirectToRoute('showAll');
        } else {
            return ['persons' => $allPeople, 'person' => $personToShare];
        }
    }

    /**
     * @Route("/share/{id}", name = "sharePost")
     * @Method("POST")
     */
    public function shareContactPostAction(Request $req, $id)
    {
        $email = trim($req->request->get('email'));

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $personToShare = $repo->find($id);

        $repoUser = $this->getDoctrine()->getRepository('ContactBoxBundle:User');
        $user = $repoUser->findByEmail($email);
        $userTo = $user[0];

        $allPeople = $repo->findByUser($this->getUser());

//        $userOwner = $this->getUser();
//        if( $personToShare->getUserOwner()->getId() != $userOwner->getId() ) {
        if(!($personToShare->getUsers()->contains($this->getUser()))) {
            return $this->redirectToRoute('showAll');
        } else {
            $personToShare->addUser($userTo);
            $userTo->addPerson($personToShare);

            $em = $this->getDoctrine()->getManager();

            $em->flush();

            return $this->redirectToRoute('showAll', ['persons' => $allPeople]);
        }

    }


}
