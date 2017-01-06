<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/users/groups")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UsersGroupsController extends BaseController
{
    /**
     * @Route("/{userId}", name="admin_users_groups", requirements={"userId" = "^\d+$"})
     */
    public function listAction(Request $request, $userId)
    {
        $user = $this->getEntityById('BaseBundle:User', $userId);

        return $this->render('AdminBundle:UsersGroups:list.html.twig', [
            'user'     => $user,
            'pagerIn'  => $this->_getUserGroups($request, $userId, 'in'),
            'pagerOut' => $this->_getUserGroups($request, $userId, 'out'),
        ]);
    }

    protected function _getUserGroups(Request $request, $userId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('g')
           ->from(Group::class, 'g')
           ->setParameter('userId', $userId)
        ;

        if ('in' == $prefix) {
           $qb->where(":userId MEMBER OF g.users");
        } else {
           $qb->where(":userId NOT MEMBER OF g.users");
        }

        if ($filter) {
            $qb
               ->andWhere('g.name LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return $this->getPager($request, $qb, $prefix);
    }

    /**
     * @Route(
     *     "/toggle/{userId}/{groupId}/{token}",
     *     name = "admin_users_groups_toggle",
     *     requirements = {"userId" = "^\d+$", "groupId" = "^\d+$"}
     * )
     */
    public function toggleAction(Request $request, $userId, $groupId, $token)
    {
        $this->checkCsrfToken('administration', $token);
        $user  = $this->getEntityById('BaseBundle:User', $userId);
        $group = $this->getEntityById('BaseBundle:Group', $groupId);

        if ($user->getGroups()->contains($group)) {
            $user->getGroups()->removeElement($group);
        } else {
            $user->getGroups()->add($group);
        }

        $em = $this->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $this->listAction($request, $userId);
    }
}
