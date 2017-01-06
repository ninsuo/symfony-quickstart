<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/administrators")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdministratorsController extends BaseController
{
    /**
     * @Route("/", name="admin_administrators")
     * @Template()
     */
    public function listAction()
    {
        $list = $this
            ->get('doctrine')
            ->getManager()
            ->getRepository('BaseBundle:User')
            ->findAll()
        ;

        return [
            'list' => $list,
            'me' => $this->getUser()->getId(),
        ];
    }

    /**
     * @Route("/toggle/{token}", name="admin_administrators_list")
     * @Template()
     */
    public function toggleAction(Request $request, $token)
    {
       if ($token !== $this->get('security.csrf.token_manager')->getToken('admin-administrators-toggle')->getValue()) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }

        $this->get('admin.storage.user')->toggleAdmin(
            $request->request->get('id')
        );

        return new Response();
    }
}
