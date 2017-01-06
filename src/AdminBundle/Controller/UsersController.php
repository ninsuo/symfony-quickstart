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
 * @Route("/users")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UsersController extends BaseController
{
    /**
     * @Route("/", name="admin_users")
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
     * @Route("/toggle/{token}", name="admin_users_toggle")
     * @Template()
     */
    public function toggleAction(Request $request, $token)
    {
       if ($token !== $this->get('security.csrf.token_manager')->getToken('administration')->getValue()) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }

        $this->get('admin.storage.user')->toggleAdmin(
            intval($request->request->get('id'))
        );

        return new Response();
    }
}
