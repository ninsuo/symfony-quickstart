<?php

namespace BaseBundle\Pagerfanta\View;

use Pagerfanta\View\TwitterBootstrap3View;

class LightView extends TwitterBootstrap3View
{
    public function getName()
    {
        return 'light';
    }

    protected function createDefaultTemplate()
    {
        throw new \RuntimeException('Template should be injected using dependancy injection.');
    }
}
