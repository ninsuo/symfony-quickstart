<?php

namespace BaseBundle\Pagerfanta\View;

use BaseBundle\Pagerfanta\Template\DomajaxTemplate;
use Pagerfanta\View\TwitterBootstrap3View;

class DomajaxView extends TwitterBootstrap3View
{
    protected function createDefaultTemplate()
    {
        return new DomajaxTemplate();
    }

    public function getName()
    {
        return 'domajax';
    }
}
