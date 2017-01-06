<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;
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
        $form = $this
           ->createNamedFormBuilder('create_permission')
           ->add('role', Type\TextType::class, [
               'label' => 'admin.permissions.create_label',
               'constraints' => [
                   new Constraints\NotBlank(),
               ],
           ])
           ->add('submit', Type\SubmitType::class, [
               'label' => 'admin.permissions.create_submit',
               'attr'  => [
                   'class'         => 'domajax',
                   'data-endpoint' => $this->generateUrl('admin_permissions_list', [
                       'selectedId' => $selectedId,
                   ]),
                   'data-output'   => '#main',
               ],
           ])
           ->getForm()
           ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $permission = new Permission();
            $permission->setRole($form->getData()['role']);

            $this->persistHandleDuplicates($form, $permission, $this->trans('admin.permissions.entity'));
        }

        $list = $this
           ->getManager('BaseBundle:Permission')
           ->findAll()
        ;

        return [
            'list'       => $list,
            'selectedId' => $selectedId,
            'create'     => $form->createView(),
        ];
    }

    /**
     * @Route("/delete/{token}/{selectedId}", name="admin_permissions_delete", defaults={"selectedId": null})
     * @Template()
     */
    public function _deleteAction(Request $request, $selectedId, $token)
    {
        if ($token !== $this->get('security.csrf.token_manager')->getToken('administration')->getValue()) {
            throw new InvalselectedIdCsrfTokenException('InvalselectedId CSRF token');
        }


        // ...


        return $this->redirect(
              $this->generateUrl('admin_permissions_list', ['selectedId' => $selectedId])
        );
    }
}
