<?php

namespace AdminBundle\Controller;

use AppBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/toggle")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ToggleController extends BaseController
{
    /**
     * @Route("/{fromType}-{fromId}/{toType}-{toId}/{token}", name = "admin_toggle")
     */
    public function toggleAction(Request $request, $fromType, $fromId, $toType, $toId, $token)
    {
        $this->checkCsrfToken('administration', $token);

        $user = $group = $role = null;
        if ($fromType === 'groups' && $toType === 'users') {
            $group = $this->getEntityById('BaseBundle:Group', $fromId);
            $user = $this->getEntityById('BaseBundle:User', $toId);
        } elseif ($fromType === 'groups' && $toType === 'roles') {
            $group = $this->getEntityById('BaseBundle:Group', $fromId);
            $role = $this->getEntityById('BaseBundle:Role', $toId);
        } elseif ($fromType === 'roles' && $toType === 'groups') {
            $role = $this->getEntityById('BaseBundle:Role', $fromId);
            $group = $this->getEntityById('BaseBundle:Group', $toId);
        } elseif ($fromType === 'roles' && $toType === 'users') {
            $role = $this->getEntityById('BaseBundle:Role', $fromId);
            $user = $this->getEntityById('BaseBundle:User', $toId);
        } elseif ($fromType === 'users' && $toType === 'groups') {
            $user = $this->getEntityById('BaseBundle:User', $fromId);
            $group = $this->getEntityById('BaseBundle:Group', $toId);
        } elseif ($fromType === 'users' && $toType === 'roles') {
            $user = $this->getEntityById('BaseBundle:User', $fromId);
            $role = $this->getEntityById('BaseBundle:Role', $toId);
        } else {
            throw $this->createNotFoundException();
        }

        if (!is_null($user) && !is_null($group)) {
            if ($user->getGroups()->contains($group)) {
                $user->getGroups()->removeElement($group);
            } else {
                $user->getGroups()->add($group);
            }
            $this->saveEntity($user);
        } elseif (!is_null($user) && !is_null($role)) {
            if ($user->getPermissions()->contains($role)) {
                $user->getPermissions()->removeElement($role);
            } else {
                $user->getPermissions()->add($role);
            }
            $this->saveEntity($user);
        } else {
            if ($group->getPermissions()->contains($role)) {
                $group->getPermissions()->removeElement($role);
            } else {
                $group->getPermissions()->add($role);
            }
            $this->saveEntity($group);
        }

        return $this->redirect(
           $this->generateUrl("admin_{$fromType}_manage", array_merge($request->query->all(), ['id' => $fromId]))
        );
    }
}
