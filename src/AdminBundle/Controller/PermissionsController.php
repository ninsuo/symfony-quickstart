<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Group;
use BaseBundle\Entity\Permission;
use BaseBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

/**
 * @Route("/permissions")
 * @Security("has_role('ROLE_ADMIN')")
 */
class PermissionsController extends BaseController
{
    /**
     * @Route("/", name="admin_permissions")
     * @Template()
     */
    public function listAction(Request $request)
    {
        $form = $this->getCreateForm($request);
        if (is_null($form)) {
            return new RedirectResponse($this->generateUrl('admin_permissions'));
        }

        $filter = $request->query->get('permission-filter');

        $qb = $this
            ->getManager()
            ->createQueryBuilder()
            ->select('p')
            ->from(Permission::class, 'p')
        ;

        if ($filter) {
            $qb
                ->where('p.name LIKE :criteria')
                ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'orderBy' => $this->orderBy($qb, Permission::class, 'p.name'),
            'pager'   => $this->getPager($qb, 'permission-'),
            'create'  => $form,
        ];
    }

    /**
     * @Route("/delete/{id}/{token}", name="admin_permissions_delete")
     * @Template()
     */
    public function deleteAction($id, $token)
    {
        $this->checkCsrfToken('admin_permissions', $token);

        $manager = $this->getManager('BaseBundle:Permission');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $this->success('admin.permissions.deleted', ['%id%' => $entity->getId()]);

        $em = $this->get('doctrine')->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_permissions'));
    }

    /**
     * @Route("/edit/name/{id}", name="_admin_permissions_edit_name")
     * @Template("BaseBundle::editOnClick.html.twig")
     */
    public function _editNameAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:Permission');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint = $this->generateUrl('_admin_permissions_edit_name', ['id' => $id]);

        $form = $this
            ->createNamedFormBuilder("edit-permission-name-{$id}", Type\FormType::class, $entity, [
                'action' => $endpoint,
            ])
            ->add('name', Type\TextType::class, [
                'label'       => 'admin.permissions.name',
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
     * @Route("/manage/{id}", name="admin_permissions_manage")
     * @Template()
     */
    public function manageAction(Request $request, $id)
    {
        $permission = $this->getEntityById('BaseBundle:Permission', $id);

        return [
            'permission' => $permission,
            'usersIn'    => $this->_getPermissionUsers($request, $id, 'user-in'),
            'usersOut'   => $this->_getPermissionUsers($request, $id, 'user-out'),
            'groupsIn'   => $this->_getPermissionGroups($request, $id, 'group-in'),
            'groupsOut'  => $this->_getPermissionGroups($request, $id, 'group-out'),
        ];
    }

    protected function getCreateForm(Request $request)
    {
        $entity = new Permission();

        $form = $this
            ->createNamedFormBuilder('create-permission', Type\FormType::class, $entity)
            ->add('name', Type\TextType::class, [
                'label'       => 'admin.permissions.name',
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

            $this->success('admin.permissions.created');

            return null;
        }

        return $form->createView();
    }

    protected function _getPermissionUsers(Request $request, $permissionId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");

        $qb = $this
            ->getManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->setParameter('permissionId', $permissionId)
        ;

        if ('user-in' == $prefix) {
            $qb->where(':permissionId MEMBER OF u.permissions');
        } else {
            $qb->where(':permissionId NOT MEMBER u.permissions');
        }

        if ($filter) {
            $qb
                ->andWhere('u.nickname LIKE :criteria OR u.contact LIKE :criteria')
                ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'order' => $this->orderBy($qb, Permission::class, 'u.nickname', 'ASC', $prefix),
            'pager' => $this->getPager($qb, $prefix),
        ];
    }

    protected function _getPermissionGroups(Request $request, $permissionId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");

        $qb = $this
            ->getManager()
            ->createQueryBuilder()
            ->select('g')
            ->from(Group::class, 'g')
            ->setParameter('permissionId', $permissionId)
        ;

        if ('group-in' == $prefix) {
            $qb->where(':permissionId MEMBER OF g.permissions');
        } else {
            $qb->where(':permissionId NOT MEMBER OF g.permissions');
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
