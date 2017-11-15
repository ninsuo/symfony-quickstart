<?php

namespace AdminBundle\Controller;

use BaseBundle\Base\BaseController;
use BaseBundle\Entity\Group;
use BaseBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/groups")
 * @Security("has_role('ROLE_ADMIN')")
 */
class GroupsController extends BaseController
{
    /**
     * @Route("/", name="admin_groups")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('g')
           ->from(Group::class, 'g')
        ;

        if ($filter) {
            $qb
               ->where('g.name LIKE :criteria OR g.notes LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'orderBy' => $this->orderBy($qb, Group::class, 'g.name'),
            'pager'   => $this->getPager($qb),
            'create'  => $this->getCreateForm($request),
        ];
    }

    protected function getCreateForm(Request $request)
    {
        $entity = new Group();

        $form = $this
           ->createNamedFormBuilder('create-group', Type\FormType::class, $entity)
           ->add('name', Type\TextType::class, [
               'label' => 'admin.groups.name',
           ])
           ->add('notes', Type\TextareaType::class, [
               'label'    => 'admin.groups.notes',
               'required' => false,
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => 'base.crud.action.save',
           ])
           ->getForm()
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($entity);
            $em->flush();

            $this->success('admin.groups.created');

            $this->redirect('admin_groups');
        }

        return $form->createView();
    }

    /**
     * @Route("/delete/{id}/{token}", name="admin_groups_delete")
     * @Template()
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $this->checkCsrfToken('administration', $token);

        $manager = $this->getManager('BaseBundle:Group');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $this->get('security')->login($this->getUser());

        $em = $this->get('doctrine')->getManager();
        $em->remove($entity);
        $em->flush();

        $this->success('admin.groups.deleted', ['%id%' => $entity->getId()]);

        return $this->redirect($this->generateUrl('admin_groups', $request->query->all()));
    }

    /**
     * @Route("/edit/name/{id}", name="_admin_groups_edit_name")
     * @Template("BaseBundle::editOnClick.html.twig")
     */
    public function _editNameAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:Group');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint = $this->generateUrl('_admin_groups_edit_name', ['id' => $id]);

        $form = $this
           ->createNamedFormBuilder("edit-name-{$id}", Type\FormType::class, $entity, [
               'action' => $endpoint,
           ])
           ->add('name', Type\TextType::class, [
               'label' => 'admin.groups.name',
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => 'base.crud.action.save',
               'attr'  => [
                   'class' => 'domajax',
               ],
           ])
           ->getForm()
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($entity);
            $em->flush();

            return [
                'text'     => $entity->getName(),
                'endpoint' => $endpoint,
            ];
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/edit/notes/{id}", name="_admin_groups_edit_notes")
     * @Template("BaseBundle::editOnClick.html.twig")
     */
    public function _editNotesAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:Group');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint = $this->generateUrl('_admin_groups_edit_notes', ['id' => $id]);

        $form = $this
           ->createNamedFormBuilder("edit-notes-{$id}", Type\FormType::class, $entity, [
               'action' => $endpoint,
           ])
           ->add('notes', Type\TextareaType::class, [
               'label' => 'admin.groups.notes',
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => 'base.crud.action.save',
               'attr'  => [
                   'class' => 'domajax',
               ],
           ])
           ->getForm()
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->get('doctrine')->getManager();
            $em->persist($entity);
            $em->flush();

            return [
                'text'     => $entity->getNotes(),
                'endpoint' => $endpoint,
            ];
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/manage/{id}", name="admin_groups_manage")
     * @Template()
     */
    public function manageAction(Request $request, $id)
    {
        $group = $this->getEntityById('BaseBundle:Group', $id);

        return [
            'group'    => $group,
            'usersIn'  => $this->_getGroupUsers($request, $id, 'user-in'),
            'usersOut' => $this->_getGroupUsers($request, $id, 'user-out'),
        ];
    }

    protected function _getGroupUsers(Request $request, $groupId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('u')
           ->from(User::class, 'u')
           ->setParameter('groupId', $groupId)
        ;

        if ('user-in' == $prefix) {
            $qb->where(':groupId MEMBER OF u.groups');
        } else {
            $qb->where(':groupId NOT MEMBER OF u.groups');
        }

        if ($filter) {
            $qb
               ->andWhere('u.nickname LIKE :criteria OR u.contact LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'order' => $this->orderBy($qb, User::class, 'u.nickname', 'ASC', $prefix),
            'pager' => $this->getPager($qb, $prefix),
        ];
    }
}
