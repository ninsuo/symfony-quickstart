<?php

namespace Fuz\QuickStartBundle\Controller;

use Fuz\QuickStartBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * User is automatically redirected here by the CaptchaListener,
 * if a route is protected and user tried to access it too much.
 */
class CaptchaController extends BaseController
{

    /**
     * @Route("/captcha/{key}", name="captcha")
     */
    public function captchaRequiredAction($key)
    {
        return $this->render('FuzQuickStartBundle:Captcha:captcha.html.twig', array(
               'key' => $key,
        ));
    }

    /**
     * @Route("/captcha/validate/{key}", name="captcha_validate")
     * @Method({"POST"})
     */
    public function captchaValidationFailedAction($key)
    {
        $this->alert('quickstart.captcha.invalid');

        return $this->captchaRequiredAction($key);
    }

}
