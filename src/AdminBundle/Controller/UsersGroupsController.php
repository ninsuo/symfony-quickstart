<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\Group;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/users/groups")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UsersGroupsController extends BaseController
{
    /**
     * @Route("/{userId}", name="admin_users_groups", requirements={"userId" = "^\d+$"})
     * @Template()
     */
    public function listAction(Request $request, $userId)
    {
        $user = $this->getEntityById('BaseBundle:User', $userId);

        return [
            'user'     => $user,
            'pagerIn'  => $this->_getUserGroups($request, $userId, 'in'),
            'pagerOut' => $this->_getUserGroups($request, $userId, 'out'),
        ];
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
            if ($this->getUser()->isEqualTo($user)) {
                $this->getUser()->removeRole('ROLE_GROUP_'.$group->getName());
                $this->get('security')->login($this->getUser());
            }
        } else {
            $user->getGroups()->add($group);
            if ($this->getUser()->isEqualTo($user)) {
                $this->getUser()->addRole('ROLE_GROUP_'.$group->getName());
                $this->get('security')->login($this->getUser());
             }
        }

        $em = $this->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect(
            $this->generateUrl('admin_users_groups', array_merge($request->query->all(), ['userId' => $userId]))
        );
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
            $qb->where(':userId MEMBER OF g.users');
        } else {
            $qb->where(':userId NOT MEMBER OF g.users');
        }

        if ($filter) {
            $qb
               ->andWhere('g.name LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return $this->getPager($request, $qb, $prefix);
    }
}
