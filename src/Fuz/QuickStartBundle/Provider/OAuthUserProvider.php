<?php

namespace Fuz\QuickStartBundle\Provider;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;

class OAuthUserProvider extends BaseUserProvider
{
    protected $userRepository;
    protected $userManager;

    public function __construct(EntityManagerInterface $em, UserManagerInterface $userManager)
    {
        $this->userRepository = $em->getRepository('FuzQuickStartBundle:User');
        $this->userManager    = $userManager;
    }

    public function loadUserByUsername($username)
    {
        list($resourceOwner, $resourceOwnerId) = json_decode($username);

        return $this->userRepository->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwner   = $response->getResourceOwner()->getName();
        $resourceOwnerId = $response->getUsername();
        $name            = $this->getNameToDisplay($resourceOwner, $response);
        $json            = json_encode(array($resourceOwner, $resourceOwnerId));

        $user = $this->userRepository->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);

        if (is_null($user)) {
            $user = $this->userManager->createUser();
            $user->setUsername($json);
            $user->setEnabled(true);
            $user->setNickname($name);
            $user->setResourceOwner($resourceOwner);
            $user->setResourceOwnerId($resourceOwnerId);
            $user->setSigninCount(1);
            $this->updateEmail($response, $user);
            $this->userManager->updateUser($user);

            return $this->loadUserByUsername($json);
        } else {
            $user->setSigninCount($user->getSigninCount() + 1);
            $this->updateEmail($response, $user);
            $this->userManager->updateUser($user);

            return $user;
        }
    }

    public function getNameToDisplay($resourceOwner, $response)
    {
        $name = null;
        switch ($resourceOwner) {
            case 'google':
                $name = $response->getNickname();
                break;
            case 'facebook':
                $name = $response->getRealName();
                break;
            case 'twitter':
                $name = $response->getNickname();
                break;
            default:
                break;
        }

        return $name;
    }

    public function updateEmail(UserResponseInterface $response, UserInterface $user)
    {
        $email = $response->getEmail();
        if ($email && $email !== $user->getEmail() && !$this->userManager->findUserBy(array('email' => $email))) {
            $user->setEmail($email);
        }
    }

    public function supportsClass($class)
    {
        return $class === 'Fuz\\QuickStartBundle\\Entity\\User';
    }
}
