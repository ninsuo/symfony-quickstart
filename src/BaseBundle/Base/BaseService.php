<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class BaseService implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;
}
