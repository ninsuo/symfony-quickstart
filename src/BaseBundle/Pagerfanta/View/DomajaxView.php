<?php

namespace BaseBundle\Pagerfanta\View;

use BaseBundle\Pagerfanta\Template\DomajaxTemplate;
use Pagerfanta\View\TwitterBootstrap3View;

class DomajaxView extends TwitterBootstrap3View
{
    public function getName()
    {
        return 'domajax';
    }

    protected function createDefaultTemplate()
    {
        return new DomajaxTemplate();
    }
}
