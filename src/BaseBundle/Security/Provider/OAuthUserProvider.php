<?php

namespace BaseBundle\Security\Provider;

use BaseBundle\Entity\User;
use BaseBundle\Traits\ServiceTrait;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class OAuthUserProvider extends BaseUserProvider implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        list($resourceOwner, $resourceOwnerId) = json_decode($username, true);

        $user = $this->em->getRepository('BaseBundle:User')
           ->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);

        if ($user) {

            if (!$user->isEnabled()) {
                throw new AuthenticationException(
                   $this->get('translator')->trans('base.error.user_not_enabled', [
                       '%id%' => $user->getId(),
                   ])
                );
            }

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

        if ($this->getParameter('user_email_restriction')
           && !preg_match($this->getParameter('user_email_restriction'), $response->getEmail())) {
            throw new AuthenticationException(
               $this->get('translator')->trans('base.error.user_email_restriction', [
                   '%email%' => $response->getEmail(),
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
            $user->setIsEnabled($this->getParameter('user_auto_enabled'));
            $user->setIsAdmin(false);
            $this->em->persist($user);
            $this->em->flush($user);
            $reload = true;
        } else {
            if ($this->getParameter('user_info_auto_update')) {
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

    public function supportsClass($class)
    {
        return $class === 'BaseBundle\\Entity\\User';
    }

    protected function injectRoles(User $user)
    {
        if ($user->isAdmin()) {
            $user->addRole('ROLE_ADMIN');

            // Setting up all possible permissions
            $all = $this->em->getRepository('BaseBundle:Group')->findAll();
            foreach ($all as $one) {
                $user->addRole('ROLE_'.mb_strtoupper($one->getName()));
            }

            return $user;
        }

        // Granted permissions
        foreach ($user->getGroups()->toArray() as $group) {
            $user->addRole('ROLE_'.strtoupper($group->getName()));
        }

        return $user;
    }
}
