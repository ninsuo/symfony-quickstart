<?php

namespace BaseBundle\Base;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;

abstract class BaseType extends AbstractType implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function get($service)
    {
        return $this->container->get($service);
    }

    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }
}
