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
     * @Route("/{fromType}-{fromId}/{toType}-{toId}/{grant}/{token}", name="admin_toggle", defaults={"grant": "default"})
     */
    public function toggleAction(Request $request, $fromType, $fromId, $toType, $toId, $grant, $token)
    {
        $this->checkCsrfToken('administration', $token);

        $user = $group = $permission = null;
        if ($fromType === 'groups' && $toType === 'users') {
            $group = $this->getEntityById('BaseBundle:Group', $fromId);
            $user  = $this->getEntityById('BaseBundle:User', $toId);
        } elseif ($fromType === 'groups' && $toType === 'permissions') {
            $group      = $this->getEntityById('BaseBundle:Group', $fromId);
            $permission = $this->getEntityById('BaseBundle:Permission', $toId);
        } elseif ($fromType === 'permissions' && $toType === 'groups') {
            $permission = $this->getEntityById('BaseBundle:Permission', $fromId);
            $group      = $this->getEntityById('BaseBundle:Group', $toId);
        } elseif ($fromType === 'permissions' && $toType === 'users') {
            $permission = $this->getEntityById('BaseBundle:Permission', $fromId);
            $user       = $this->getEntityById('BaseBundle:User', $toId);
        } elseif ($fromType === 'users' && $toType === 'groups') {
            $user  = $this->getEntityById('BaseBundle:User', $fromId);
            $group = $this->getEntityById('BaseBundle:Group', $toId);
        } elseif ($fromType === 'users' && $toType === 'permissions') {
            $user       = $this->getEntityById('BaseBundle:User', $fromId);
            $permission = $this->getEntityById('BaseBundle:Permission', $toId);
        } else {
            throw $this->createNotFoundException();
        }

        if (!is_null($user) && !is_null($group)) {
            if ($user->getGroups()->contains($group)) {
                $user->removeGroup($group);
            } else {
                $user->addGroup($group);
            }
            $this->saveEntity($user);
        } elseif (!is_null($user) && !is_null($permission)) {
            if ('granted' === $grant) {
                if ($user->getPermissions()->contains($permission)) {
                    $user->removePermission($permission);
                } else {
                    $user->addPermission($permission);
                }
            } else {
                if ($user->getDeniedPermissions()->contains($permission)) {
                    $user->removeDeniedPermission($permission);
                } else {
                    $user->addDeniedPermission($permission);
                }
            }
            $this->saveEntity($user);
        } else {
            if ('granted' === $grant) {
                if ($group->getPermissions()->contains($permission)) {
                    $group->removePermission($permission);
                } else {
                    $group->addPermission($permission);
                }
            } else {
                if ($group->getDeniedPermissions()->contains($permission)) {
                    $group->removeDeniedPermission($permission);
                } else {
                    $group->addDeniedPermission($permission);
                }
            }
            $this->saveEntity($group);
        }

        if ($grant) {
            $toType = "{$grant}-{$toType}";
        }

        return $this->redirect(
           $this->generateUrl("admin_{$fromType}_manage", array_merge($request->query->all(), ['id' => $fromId])).'#manage-'.$toType
        );
    }
}
