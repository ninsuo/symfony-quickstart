<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\PermissionType;
use AppBundle\Base\BaseController;
use BaseBundle\Entity\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\InvalselectedIdCsrfTokenException;

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
        $form = $this->_initForm($request, $selectedId, 'create_permission');

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
     * @Route("/edit/{token}/{selectedId}", name="admin_permissions_edit", defaults={"selectedId": null})
     * @Template()
     */
    public function _editAction(Request $request, $token, $selectedId)
    {
        if ($token !== $this->get('security.csrf.token_manager')->getToken('administration')->getValue()) {
            throw new InvalselectedIdCsrfTokenException('InvalselectedId CSRF token');
        }

        $id = $request->request->get('id');
        $this->get('admin.storage.permission')->deletePermission($id);

        if (intval($selectedId) == intval($id)) {
            $selectedId = null;
        }

        return $this->redirect(
            $this->generateUrl('admin_permissions_list', [
                'selectedId' => $selectedId,
            ])
        );
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

        return $this->redirect(
            $this->generateUrl('admin_permissions_list', [
                'selectedId' => $selectedId,
            ])
        );
    }

    /**
     * @param Request $request
     * @param int $selectedId
     * @param string $name
     * @return FormInterface
     */
    protected function _initForm(Request $request, $selectedId, $name)
    {
        $form = $this
           ->get('form.factory')
           ->createNamed($name, PermissionType::class)
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $permission = new Permission();
            $permission->setRole($form->getData()['role']);

            $this->get('base.doctrine.helper')->persistHandleDuplicates($form, $permission, $this->trans('admin.permissions.entity'));
        }

        return $form;
    }
}
