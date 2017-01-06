<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\User;
use BaseBundle\Entity\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;

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
                ->where('g.name LIKE :criteria')
                ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'pager' => $this->getPager($request, $qb),
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

        $em = $this->get('doctrine')->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_groups'));
    }

    /**
     * @Route("/edit/name/{id}", name="admin_groups_edit_name")
     * @Template("AdminBundle::_editOnClick.html.twig")
     */
    public function _editNameAction(Request $request, $id)
    {
        $manager = $this->getManager('BaseBundle:Group');

        $entity = $manager->findOneById($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $endpoint = $this->generateUrl('admin_groups_edit_name', ['id' => $id]);

        $form = $this
            ->createNamedFormBuilder("edit-name-{$id}", Type\FormType::class, $entity, [
                'action' => $endpoint,
            ])
            ->add('name', Type\TextType::class, [
                'label' => "admin.groups.name",
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => "base.crud.action.save",
                'attr' => [
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
                'text' => $entity->getName(),
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
            ->createNamedFormBuilder("create", Type\FormType::class, $entity)
            ->add('name', Type\TextType::class, [
                'label' => "admin.groups.name",
            ])
            ->add('submit', Type\SubmitType::class, [
                'label' => "base.crud.action.save",
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
