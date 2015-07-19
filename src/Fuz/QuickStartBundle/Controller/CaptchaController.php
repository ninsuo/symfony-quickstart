<?php

namespace Fuz\QuickStartBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Fuz\QuickStartBundle\Base\BaseController;

/**
 * User is automatically redirected here by the CaptchaListener,
 * if a route is protected and user tried to access it too much.
 */
class CaptchaController extends BaseController
{

    /**
     * @Route("/captcha/{key}", name="captcha")
     * @Method({"GET"})
     */
    public function captchaRequiredAction($key)
    {
        return $this->render('FuzQuickStartBundle:Captcha:captcha.html.twig', array(
               'key' => $key,
        ));
    }

    /**
     * @Route("/captcha/validate/{key}", name="captcha")
     * @Method({"POST"})
     */
    public function captchaValidate($key)
    {

    }

}
