<?php

namespace Fuz\QuickStartBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Fuz\QuickStartBundle\Services\Routing;

class HWIOAuthBundleListener implements EventSubscriberInterface
{

    protected $routing;
    protected $services;

    public function __construct(Routing $routing, $isGoogleEnabled, $isFacebookEnabled, $isTwitterEnabled)
    {
        $this->routing              = $routing;
        $this->services['google']   = $isGoogleEnabled;
        $this->services['facebook'] = $isFacebookEnabled;
        $this->services['twitter']  = $isTwitterEnabled;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        try {
            $currentRoute = $this->routing->getCurrentRoute($request);
        } catch (ResourceNotFoundException $ex) {
            return;
        }
        if (is_null($currentRoute)) {
            return;
        }

        if ('connect' === $currentRoute['name'] && isset($currentRoute['params']['service'])) {
            $this->checkService($currentRoute['params']['service']);
        }

        if ('_login' === substr($currentRoute['name'], -6)) {
            $this->checkService(substr($currentRoute['name'], 0, -6));
        }
    }

    public function checkService($service)
    {
            if (isset($this->services[$service]) && !$this->services[$service]) {
                throw new NotFoundHttpException();
            }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }

}
