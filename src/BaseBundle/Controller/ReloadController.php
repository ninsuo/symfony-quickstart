<?php

namespace BaseBundle\Controller;

use BaseBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ReloadController extends BaseController
{
    /**
     * Get back to the previous route.
     *
     * @Route("/reload", name="reload")
     * @Method({"GET"})
     */
    public function reloadAction(Request $request)
    {
        return $this->goBack($request);
    }
}
