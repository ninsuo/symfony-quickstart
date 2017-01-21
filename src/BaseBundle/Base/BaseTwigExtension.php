<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

abstract class BaseTwigExtension extends \Twig_Extension implements ContainerAwareInterface
{
    use ServiceTrait;
}
