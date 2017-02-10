<?php

namespace BaseBundle\Provider;

use BaseBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Translation\TranslatorInterface;

class OAuthUserProvider extends BaseUserProvider
{
    protected $em;
    protected $translator;
    protected $registrationRestriction;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, $registrationRestriction)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->registrationRestriction = $registrationRestriction;
    }

    public function loadUserByUsername($username)
    {
        list($resourceOwner, $resourceOwnerId) = json_decode($username, true);

        $user = $this->em->getRepository('BaseBundle:User')
           ->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);

        if ($user) {
            $this->injectRoles($user);
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

        if ($this->registrationRestriction && !preg_match($this->registrationRestriction, $response->getEmail())) {
            throw new AuthenticationException(
               $this->translator->trans('base.error.registration_restriction', [
                   '%email%' => $response->getEmail()
               ])
            );
        }

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

        return $user;
    }

    protected function injectRoles(User $user)
    {
        if ($user->isAdmin()) {
            $user->addRole('ROLE_ADMIN');
        }

        // Groupes
        foreach ($user->getGroups()->toArray() as $group) {
            foreach ($group->getPermissions() as $permission) {
                $user->addRole('ROLE_' . $permission->getName());
            }
        }

        // Permissions
        foreach ($user->getPermissions() as $permission) {
            $user->addRole('ROLE_' . $permission->getName());
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return $class === 'BaseBundle\\Entity\\User';
    }
}
