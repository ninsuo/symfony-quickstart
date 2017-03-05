<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

abstract class BaseVoter extends Voter implements ContainerAwareInterface
{
    use ServiceTrait;
    use ContainerAwareTrait;

    protected $decisionManager;

    public function setDecisionManager(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function isAdmin()
    {
        return $this->decisionManager->decide(
           $this->get('security.token_storage')->getToken(),
           ['ROLE_ADMIN']
        );
    }
}
