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
        //żeby widzieć tylko swoje grupy:
        $groups = $repo->selectGroups($this->getUser());

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

        if ($form->isValid() && $form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            $group->setUserOwner($user);
            $user->addContactGroup($group);

            $em->persist($group);

            $em->flush();
        }
        return $this->redirectToRoute('showAllGroups');
    }


    /**
     * @Route("/showByGroup/{id}", name = "showByGroup")
     * @Template("ContactBoxBundle:Groups:showGroup.html.twig")
     */
    public function getAllFromGroupAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Groups');
        $group = $repo->find($id);
        if ($group->getUserOwner() == $this->getUser()) {
            $people = $group->getPersons();
            return ['persons' => $people, 'group' => $group];
        } else {
            return $this->redirectToRoute('showAllGroups');
        }
    }

    // @TODO: Adding removing options.

    /**
     * @Route("/showAllGroups", name = "showAllGroups")
     * @Template("ContactBoxBundle:Groups:showAllGroups.html.twig")
     */
    public function showAllGroupsAction()
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Groups');
        $groups = $repo->selectGroups($this->getUser());

        return ['groups' => $groups];
    }

    /**
     * @Route("/removePersonFromGroup/{groupId}/{personId}", name = "removePersonFromGroup")
     *
     */
    public function removePersonFromGroupAction($groupId, $personId)
    {
        $repo = $this->getDoctrine()->getRepository('ContactBoxBundle:Groups');
        $group = $repo->find($groupId);
        $peopleRepo = $this->getDoctrine()->getRepository('ContactBoxBundle:Person');
        $person = $peopleRepo->find($personId);

        if ($group->getUserOwner() == $this->getUser() && $group->getPersons()->contains($person)) {
            $group->removePerson($person);
            $person->removeGroup($group);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

        }
        return $this->redirectToRoute('showByGroup', ['id' => $groupId]);
    }

}
