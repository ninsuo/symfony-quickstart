<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Group;
use BaseBundle\Entity\Role;
use BaseBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints;

/**
 * @Route("/roles")
 * @Security("has_role('ROLE_ADMIN')")
 */
class RolesController extends BaseController
{
    /**
     * @Route("/", name="admin_roles")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $form = $this->getCreateForm($request);
        if (is_null($form)) {
            return new RedirectResponse($this->generateUrl('admin_roles'));
        }

        $filter = $request->query->get('role-filter');

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('r')
           ->from(Role::class, 'r')
        ;

        if ($filter) {
            $qb
               ->where('r.name LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'orderBy' => $this->orderBy($qb, Role::class, 'r.name'),
            'pager'   => $this->getPager($qb, 'role-'),
            'create'  => $form,
        ];
    }

    protected function getCreateForm(Request $request)
    {
        $entity = new Role();

        $form = $this
           ->createNamedFormBuilder('create-role', Type\FormType::class, $entity)
           ->add('name', Type\TextType::class, [
               'label'       => 'admin.roles.name',
               'constraints' => [],
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

            $this->success("admin.roles.created");

            return null;
        }

        return $form->createView();
    }

    /**
     * @Route("/delete/{id}/{token}", name="admin_roles_delete")
     * @Template()
     */
    public function deleteAction($id, $token)
    {
        $this->checkCsrfToken('admin_roles', $token);

        $manager = $this->getManager('BaseBundle:Role');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $this->success("admin.roles.deleted", ['%id%' => $entity->getId()]);

        $em = $this->get('doctrine')->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_roles'));
    }

    /**
     * @Route("/edit/name/{id}", name="_admin_roles_edit_name")
     * @Template("BaseBundle::editOnClick.html.twig")
     */
    public function _editNameAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:Role');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint = $this->generateUrl('_admin_roles_edit_name', ['id' => $id]);

        $form = $this
           ->createNamedFormBuilder("edit-role-name-{$id}", Type\FormType::class, $entity, [
               'action' => $endpoint,
           ])
           ->add('name', Type\TextType::class, [
               'label'       => 'admin.roles.name',
               'constraints' => [],
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
     * @Route("/manage/{id}", name="admin_roles_manage")
     * @Template()
     */
    public function manageAction(Request $request, $id)
    {
        $role = $this->getEntityById('BaseBundle:Role', $id);

        return [
            'role' => $role,
            'usersIn' => $this->_getRoleUsers($request, $id, 'user-in'),
            'usersOut' => $this->_getRoleUsers($request, $id, 'user-out'),
            'groupsIn' => $this->_getRoleGroups($request, $id, 'group-in'),
            'groupsOut' => $this->_getRoleGroups($request, $id, 'group-out'),
        ];
    }

    protected function _getRoleUsers(Request $request, $roleId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('u')
           ->from(User::class, 'u')
           ->setParameter('roleId', $roleId)
        ;

        if ('user-in' == $prefix) {
            $qb->where(':roleId MEMBER OF u.permissions');
        } else {
            $qb->where(':roleId NOT MEMBER u.permissions');
        }

        if ($filter) {
            $qb
               ->andWhere('u.nickname LIKE :criteria OR u.contact LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'order' => $this->orderBy($qb, Role::class, 'u.nickname', 'ASC', $prefix),
            'pager' => $this->getPager($qb, $prefix),
        ];
    }

    protected function _getRoleGroups(Request $request, $roleId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");


        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('g')
           ->from(Group::class, 'g')
           ->setParameter('roleId', $roleId)
        ;

        if ('group-in' == $prefix) {
            $qb->where(':roleId MEMBER OF g.permissions');
        } else {
            $qb->where(':roleId NOT MEMBER OF g.permissions');
        }

        if ($filter) {
            $qb
               ->andWhere('g.name LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'order' => $this->orderBy($qb, Group::class, 'g.name', 'ASC', $prefix),
            'pager' => $this->getPager($qb, $prefix),
        ];
    }
}
