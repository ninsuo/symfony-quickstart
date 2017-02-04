<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use BaseBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/groups/users")
 * @Security("has_role('ROLE_ADMIN')")
 */
class GroupsUsersController extends BaseController
{
    /**
     * @Route("/{groupId}", name="admin_groups_users", requirements={"groupId" = "^\d+$"})
     * @Template()
     */
    public function listAction(Request $request, $groupId)
    {
        $group = $this->getEntityById('BaseBundle:Group', $groupId);

        return [
            'group'    => $group,
            'pagerIn'  => $this->_getGroupUsers($request, $groupId, 'in'),
            'pagerOut' => $this->_getGroupUsers($request, $groupId, 'out'),
        ];
    }

    /**
     * @Route(
     *     "/toggle/{groupId}/{userId}/{token}",
     *     name = "admin_groups_users_toggle",
     *     requirements = {"groupId" = "^\d+$", "userId" = "^\d+$"}
     * )
     */
    public function toggleAction(Request $request, $groupId, $userId, $token)
    {
        $this->checkCsrfToken('administration', $token);
        $group = $this->getEntityById('BaseBundle:Group', $groupId);
        $user  = $this->getEntityById('BaseBundle:User', $userId);

        if ($group->getUsers()->contains($user)) {
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
            $this->generateUrl('admin_groups_users', array_merge($request->query->all(), ['groupId' => $groupId]))
        );
    }

    protected function _getGroupUsers(Request $request, $groupId, $prefix)
    {
        $filter = $request->query->get("filter-{$prefix}");

        $qb = $this
           ->getManager()
           ->createQueryBuilder()
           ->select('u')
           ->from(User::class, 'u')
           ->setParameter('groupId', $groupId)
        ;

        if ('in' == $prefix) {
            $qb->where(':groupId MEMBER OF u.groups');
        } else {
            $qb->where(':groupId NOT MEMBER OF u.groups');
        }

        if ($filter) {
            $qb
               ->andWhere('u.nickname LIKE :criteria OR u.contact LIKE :criteria')
               ->setParameter('criteria', '%'.$filter.'%')
            ;
        }

        return $this->getPager($qb, $prefix);
    }
}
