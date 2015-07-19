<?php

namespace Fuz\QuickStartBundle\EventListener;

use Fuz\QuickStartBundle\Services\Captcha;
use Fuz\QuickStartBundle\Tools\Math;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CaptchaListener implements EventSubscriberInterface
{
    const MAX_CACHED_CAPTCHAS = 10;

    protected $router;
    protected $captcha;
    protected $config;

    public function __construct(Router $router, Captcha $captcha, $config)
    {
        $this->router  = $router;
        $this->captcha = $captcha;
        $this->config  = $config;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $route   = $session->get('current_route');

        if (array_key_exists($route['name'], $this->config['strategies'])) {
            $test = $this->captcha->check($request, $route['name']);
            if (false === $test) {
                $this->redirectToCaptchaPage($session, $request, $route);
            }
        }
    }

    protected function redirectToCaptchaPage(GetResponseEvent $event, array $route)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $cache   = $session->get('noCaptcha');
        $key     = Math::rand();

        $cache[$key] = array(
            'route'   => $route,
            'context' => array(
                'request' => $request->request,
                'query'   => $request->query,
            ),
        );
        if (count($cache) > self::MAX_CACHED_CAPTCHAS) {
            $cache = array_slice($cache, 1);
        }

        $session->set('noCaptcha', $cache);

        $url      = $this->router->generate('captcha', array('key' => $key));
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 20)),
        );
    }

}
