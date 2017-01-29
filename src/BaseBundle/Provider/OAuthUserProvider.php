<?php

namespace BaseBundle\Provider;

use BaseBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;

class OAuthUserProvider extends BaseUserProvider
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        list($resourceOwner, $resourceOwnerId) = json_decode($username, true);

        $user = $this->em->getRepository('BaseBundle:User')
           ->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);

        if ($user) {
            if ($user->isAdmin()) {
                $user->addRole('ROLE_ADMIN');
            }
        }

        return $user;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwner   = $response->getResourceOwner()->getName();
        $resourceOwnerId = $response->getUsername();
        $name            = $response->getRealName();
        $json            = json_encode([$resourceOwner, $resourceOwnerId]);
        $user            = $this->loadUserByUsername($json);

        $reload = false;
        if (is_null($user)) {
            $user = new User();
            $user->setResourceOwner($resourceOwner);
            $user->setResourceOwnerId($resourceOwnerId);
            $user->setNickname($name);
            $user->setContact($response->getEmail());
            $user->setPicture($response->getProfilePicture());
            $user->setSigninCount(1);
            $user->setIsAdmin(false);
            $user->setIsFrozen(false);
            $this->em->persist($user);
            $this->em->flush($user);
            $reload = true;
        } else {
            if (!$user->isFrozen()) {
                $user->setNickname($name);
                $user->setContact($response->getEmail());
                $user->setPicture($response->getProfilePicture());
            }
            $user->setSigninCount($user->getSigninCount() + 1);
            $this->em->persist($user);
            $this->em->flush($user);
        }

        if ($reload) {
            return $this->loadUserByUsername($json);
        }

        foreach ($user->getGroups()->toArray() as $group) {
            $user->addRole('GROUP_'.$group->getName());
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return $class === 'BaseBundle\\Entity\\User';
    }
}
