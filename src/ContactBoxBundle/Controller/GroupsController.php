<?php

namespace ContactBoxBundle\Controller;

use ContactBoxBundle\Entity\Groups;
use ContactBoxBundle\Entity\Person;
use ContactBoxBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/addressBook")
 */
class GroupsController extends Controller
{
    public function generateGroupForm($group, $action)
    {
        $form = $this->createFormBuilder($group);
        $form->add('name', 'text', array('trim' => true));
        $form->add('save', 'submit');
        $form->setAction($action);

        $groupForm = $form->getForm();

        return $groupForm;
    }

    /**
     * @Route("/addGroup", name = "showNewGroup")
     * @Template("ContactBoxBundle:Groups:newGroup.html.twig")
     * @Method("GET")
     */
    public function newGroupAction()
    {
        $group = new Groups();
        $groupForm = $this->generateGroupForm($group, $this->generateUrl('newGroup'));

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Groups');
        $groups = $repo->findAll();

        return ['formGroups' => $groupForm->createView(), 'groups' => $groups];
    }

    /**
     * @Route("/addGroup", name = "newGroup")
     * @Template("ContactBoxBundle:Groups:newGroup.html.twig")
     * @Method("POST")
     */
    public function newGroupPostAction(Request $req)
    {
        /** @var User $user */
        $user = $this->getUser();
        $group = new Groups();

        $form = $this->generateGroupForm($group, $this->generateUrl('newGroup'));
        $form->handleRequest($req);


        if($form->isValid() && $form->isSubmitted()) {


            $em = $this->getDoctrine()->getManager();
            $group->setUserOwner($user);
            $user->addContactGroup($group);

            $em->persist($group);

            $em->flush();
        }

        return $this->redirectToRoute('showAllGroups');
    }


    /**
     * @Route("showByGroup/{id}", name = "showByGroup")
     * @Template("ContactBoxBundle:Groups:showGroup.html.twig")
     */
    public function getAllFromGroupAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Groups');
        $group = $repo->find($id);

        $people = $group->getPersons();

        return ['persons' => $people, 'group' => $group];
    }

    /**
     * @Route("showAllGroups", name = "showAllGroups")
     * @Template("ContactBoxBundle:Groups:showAllGroups.html.twig")
     */
    public function showAllGroupsAction()
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Groups');
        $groups = $repo->findAll();

        return ['groups' => $groups];
    }





    /**
     * @Route("/searchByFN", name = "searchByFirstName")
     * @Method("GET")
     *
     */
    public function searchByFNAction()
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $allPeople = $repo->findAll();

        return $this->render('ContactBoxBundle:Person:sth2.html.twig', array('persons' => $allPeople));
    }

    /**
     * @Route("/searchByFN", name = "searchByFirstNamePost")
     * @Method("POST")
     *
     */
    public function searchByFNPostAction(Request $req)
    {

        $name = trim($req->request->get('name'));

        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $people = $repo->searchByFirstName($name);


        return $this->render('ContactBoxBundle:Person:showAll.html.twig', array('persons' => $people, 'searchName' => $name));

    }


}
