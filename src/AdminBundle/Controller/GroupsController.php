<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Group;
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
            'pager'  => $this->getPager($qb),
            'create' => $this->getCreateForm($request),
        ];
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

        $this->getUser()->removeRole('ROLE_GROUP_'.$entity->getName());
        $this->get('security')->login($this->getUser());

        $em = $this->get('doctrine')->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_groups'));
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

    protected function getCreateForm(Request $request)
    {
        $entity = new Group();

        $form = $this
            ->createNamedFormBuilder('create', Type\FormType::class, $entity)
            ->add('name', Type\TextType::class, [
                'label' => 'admin.groups.name',
            ])
            ->add('notes', Type\TextareaType::class, [
                'label' => 'admin.groups.notes',
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
        }

        return $form->createView();
    }
}
