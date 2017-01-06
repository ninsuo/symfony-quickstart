<?php

namespace BaseBundle\Base;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class BaseCommand extends ContainerAwareCommand
{
    public function get($service)
    {
        return $this->container->get($service);
    }

    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
    }
}
