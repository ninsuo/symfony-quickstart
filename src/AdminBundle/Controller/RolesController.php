<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Role;
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
}
