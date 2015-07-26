<?php

namespace Fuz\QuickStartBundle\EventListener;

use Fuz\QuickStartBundle\Services\Captcha;
use Fuz\QuickStartBundle\Tools\Math;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CaptchaListener implements EventSubscriberInterface
{

    const MAX_CACHED_CAPTCHAS = 10;

    protected $router;
    protected $captcha;
    protected $controller;
    protected $config;
    protected $context  = array('attributes', 'request', 'query', 'server', 'cookies', 'headers');
    protected $redirect = null;

    public function __construct(Router $router, Captcha $captcha, Controller $controller, $config)
    {
        $this->router     = $router;
        $this->captcha    = $captcha;
        $this->controller = $controller;
        $this->config     = $config;
    }

    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();
        $route   = $session->get('current_route');

        if ('captcha_validate' === $route['name'] && isset($route['params']['key'])) {
            $cache = $session->get('noCaptcha');
            $key   = $route['params']['key'];
            if (array_key_exists($key, $cache)) {
                $strategy = $cache[$key]['route']['name'];
                if (true === $this->captcha->check($request, $strategy)) {
                    $this->redirectToOriginalController($event, $key);
                }
            }
        } else if ('captcha' !== $route['name'] && array_key_exists($route['name'], $this->config['strategies'])) {
            $strategy = $this->config['strategies'][$route['name']];
            if (empty($strategy['method']) || in_array(strtoupper($request->getMethod()), $strategy['method'])) {
                if (false === $this->captcha->check($request, $route['name'])) {
                    $this->redirectToCaptchaPage($event, $route);
                }
            }
        }
    }

    protected function redirectToCaptchaPage(GetResponseEvent $event, array $route)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $cache   = $session->get('noCaptcha');
        $key     = Math::rand();

        $cache[$key]['route'] = $route;
        foreach ($this->context as $attribute) {
            $cache[$key][$attribute] = $request->{$attribute};
        }

        if (count($cache) > self::MAX_CACHED_CAPTCHAS) {
            $cache = array_slice($cache, 1);
        }

        $session->set('noCaptcha', $cache);

        $url      = $this->router->generate('captcha', array('key' => $key));
        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }

    protected function redirectToOriginalController(GetResponseEvent $event, $key)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $cache   = $session->get('noCaptcha');

        if (!$cache || !array_key_exists($key, $cache)) {
            $url      = $this->router->generate('home');
            $response = new RedirectResponse($url);
            $event->setResponse($response);
            return;
        }

        foreach ($this->context as $attribute) {
            $request->{$attribute} = $cache[$key][$attribute];
        }

        $this->redirect = $cache[$key]['route'];

        unset($cache[$key]);
        $session->set('noCaptcha', $cache);
    }

    public function onController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!is_null($this->redirect)) {
            $controller = $this->redirect['params'];
            $event->setController(function() use ($controller) {
                return $this->controller->forward($controller['_controller'], $controller);
            });
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST    => array(array('onRequest', 10)),
            KernelEvents::CONTROLLER => array(array('onController', 10)),
        );
    }

}
