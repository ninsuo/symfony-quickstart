<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Type\SearchType;
use AppBundle\Base\BaseController;
use BaseBundle\Entity\User;
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
    public function listAction(Request $request)
    {
        $filter = $request->query->get('filter');

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('u')
           ->from(User::class, 'u')
        ;

        if ($filter) {
            $qb
               ->where('u.nickname LIKE :criteria OR u.contact LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return [
            'pager'  => $this->getPager($request, $qb),
            'me'     => $this->getUser()->getId(),
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
