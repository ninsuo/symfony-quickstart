<?php

namespace BaseBundle\Pagerfanta\View;

use Pagerfanta\View\TwitterBootstrap3View;

class LightView extends TwitterBootstrap3View
{
    protected function createDefaultTemplate()
    {
        throw new \RuntimeException('Template should be injected using dependancy injection.');
    }

    public function getName()
    {
        return 'light';
    }
}
