<?php

namespace BaseBundle\Provider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;
use BaseBundle\Entity\User;

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
            $user->setUsername($username);
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
            $user->setUsername($json);
            $user->setNickname($name);
            $user->setContact($response->getEmail());
            $user->setSigninCount(1);
            $this->em->persist($user);
            $this->em->flush($user);
            $reload = true;
        } else {
            $user->setUsername($json);
            $user->setNickname($name);
            $user->setContact($response->getEmail());
            $user->setSigninCount($user->getSigninCount() + 1);
            $this->em->persist($user);
            $this->em->flush($user);
        }

        if ($reload) {
            return $this->loadUserByUsername($json);
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return $class === 'BaseBundle\\Entity\\User';
    }
}
