<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class BaseCommand extends Command implements ContainerAwareInterface
{
    use ServiceTrait;
    use ContainerAwareTrait;
}
