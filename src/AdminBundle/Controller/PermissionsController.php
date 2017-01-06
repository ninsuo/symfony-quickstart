<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalselectedIdCsrfTokenException;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;

/**
 * @Route("/permissions")
 * @Security("has_role('ROLE_ADMIN')")
 */
class PermissionsController extends BaseController
{
    /**
     * @Route("/", name="admin_permissions", defaults={"selectedId":null})
     * @Template()
     */
    public function indexAction($selectedId)
    {
        return ['selectedId' => $selectedId];
    }

    /**
     * @Route("/list/{selectedId}", name="admin_permissions_list", defaults={"selectedId": null})
     * @Template()
     */
    public function _listAction(Request $request, $selectedId)
    {
        if ($id = $request->request->get('id')) {
            $this->treatForm($request, $selectedId, $id);
        }

        $form = $this->treatForm($request, $selectedId);

        $list = $this
           ->getManager('BaseBundle:Permission')
           ->findAll(['role' => 'ASC'])
        ;

        return [
            'list'       => $list,
            'selectedId' => $selectedId,
            'create'     => $form->createView(),
        ];
    }

    /**
     * @Route("/edit/{selectedId}", name="admin_permissions_edit", defaults={"selectedId": null})
     * @Template()
     */
    public function _editAction(Request $request, $selectedId)
    {
        $id   = $request->request->get('id');
        $form = $this->treatForm($request, $selectedId, $id);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/delete/{token}/{selectedId}", name="admin_permissions_delete", defaults={"selectedId": null})
     * @Template()
     */
    public function _deleteAction(Request $request, $token, $selectedId)
    {
        if ($token !== $this->get('security.csrf.token_manager')->getToken('administration')->getValue()) {
            throw new InvalselectedIdCsrfTokenException('InvalselectedId CSRF token');
        }

        $id = $request->request->get('id');
        $this->get('admin.storage.permission')->deletePermission($id);

        if (intval($selectedId) == intval($id)) {
            $selectedId = null;
        }

        return $this->redirectToRoute('admin_permissions_list', [
               'selectedId' => $selectedId,
        ]);
    }

    /**
     * @param Request $request
     * @param int $selectedId
     * @param int $id
     *
     * @return FormInterface
     */
    protected function treatForm(Request $request, $selectedId, $id = null)
    {
        $manager = $this->getManager('BaseBundle:Permission');

        $entity = new Permission();
        if (!is_null($id)) {
            $entity = $manager->findOneById($id) ? : $entity;
        }

        $action = $id ? 'update' : 'create';

        $form = $this
           ->createNamedFormBuilder($id ? "edit-permission-{$id}" : 'create-permission', Type\FormType::class, $entity)
           ->add('role', Type\TextType::class, [
               'label'       => "admin.permissions.{$action}_label",
               'constraints' => [
                   new Constraints\NotBlank(),
               ],
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => "admin.permissions.{$action}_submit",
               'attr'  => [
                   'class'           => 'domajax',
                   'data-endpoint'   => $this->generateUrl('admin_permissions_list', [
                       'selectedId' => $selectedId,
                   ]),
                   'data-input-attr' => 'id',
                   'data-id'         => $id,
                   'data-output'     => '#permissions',
               ],
           ])
           ->getForm()
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('base.doctrine.helper')->persistHandleDuplicates($form, $entity, $this->trans('admin.permissions.entity'));
        }

        return $form;
    }
}
