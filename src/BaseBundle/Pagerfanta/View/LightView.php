<?php

namespace BaseBundle\Pagerfanta\View;

use BaseBundle\Pagerfanta\Template\LightTemplate;
use Pagerfanta\View\TwitterBootstrap3View;

class LightView extends TwitterBootstrap3View
{
    protected function createDefaultTemplate()
    {
        return new LightTemplate();
    }

    public function getName()
    {
        return 'light';
    }
}
