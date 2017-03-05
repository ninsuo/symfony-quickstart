<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Form\AbstractType;

abstract class BaseType extends AbstractType implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;
}
