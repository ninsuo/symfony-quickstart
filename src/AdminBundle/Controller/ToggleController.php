<?php

namespace AdminBundle\Controller;

use BaseBundle\Base\BaseController;
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
        } elseif ($fromType === 'users' && $toType === 'groups') {
            $user  = $this->getEntityById('BaseBundle:User', $fromId);
            $group = $this->getEntityById('BaseBundle:Group', $toId);
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
        }

        if ($grant) {
            $toType = "{$grant}-{$toType}";
        }

        return $this->redirect(
           $this->generateUrl("admin_{$fromType}_manage", array_merge($request->query->all(), ['id' => $fromId])).'#manage-'.$toType
        );
    }
}
