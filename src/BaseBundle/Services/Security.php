<?php

namespace BaseBundle\Services;

use Doctrine\ORM\Proxy\Proxy;
use EasyCorp\Bundle\EasySecurityBundle\Security\Security as BaseSecurity;
use HWI\Bundle\OAuthBundle\Security\Core\Authentication\Token\OAuthToken;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\User\UserInterface;

class Security extends BaseSecurity
{
    use ContainerAwareTrait;

    /**
     * This method is used to store a real entity and not a doctrine proxy
     * on the tokenstorage (they internally do a get_class and if the entity
     * was lazily loaded, it will be an instance of a proxy).
     *
     * @param mixed $proxy
     *
     * @return mixed
     */
    public function getRealEntity($proxy)
    {
        if ($proxy instanceof Proxy) {
            $metadata              = $this->getManager()->getMetadataFactory()->getMetadataFor(get_class($proxy));
            $class                 = $metadata->getName();
            $entity                = new $class();
            $reflectionSourceClass = new \ReflectionClass($proxy);
            $reflectionTargetClass = new \ReflectionClass($entity);
            foreach ($metadata->getFieldNames() as $fieldName) {
                $reflectionPropertySource = $reflectionSourceClass->getProperty($fieldName);
                $reflectionPropertySource->setAccessible(true);
                $reflectionPropertyTarget = $reflectionTargetClass->getProperty($fieldName);
                $reflectionPropertyTarget->setAccessible(true);
                $reflectionPropertyTarget->setValue($entity, $reflectionPropertySource->getValue($proxy));
            }

            return $entity;
        }

        return $proxy;
    }

    /**
     * {@inheritdoc}
     */
    public function login(UserInterface $user, $firewallName = 'main')
    {
        $token = new OAuthToken(null, $user->getRoles());
        $token->setUser($this->getRealEntity($user));
        $token->setAuthenticated(true);
        $this->container->get('security.token_storage')->setToken($token);
        $this->container->get('session')->set("_security_{$firewallName}", serialize($token));
        $this->container->get('session')->save();

        return $this;
    }

    /**
     * This method should only be used to enforce login on development
     * environment (when you don't have an internet connection for example)
     * or on demo websites where visitors can try features requiring authentication.
     *
     * @param int $id
     * 
     * @return Security
     */
    public function loginById($id)
    {
        $user = $this->container
           ->get('doctrine')
           ->getManager()
           ->getRepository('BaseBundle:User')
           ->findOneById($id);

        $this->login(
            $this->container
                ->get('base.oauth_user_provider')
                ->loadUserByUsername($user->getUsername())
        );

        return $this;
    }
}
