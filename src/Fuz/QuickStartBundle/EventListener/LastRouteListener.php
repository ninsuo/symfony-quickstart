<?php

namespace Fuz\QuickStartBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Fuz\QuickStartBundle\Services\Routing;

class LastRouteListener implements EventSubscriberInterface
{
    protected $routing;

    public function __construct(Routing $routing)
    {
        $this->routing = $routing;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        try {
            $currentRoute = $this->routing->getCurrentRoute($request);
        } catch (ResourceNotFoundException $ex) {
            return;
        }
        if (is_null($currentRoute)) {
            return;
        }

        $session = $request->getSession();
        $previousRoute = $session->get('current_route', array());
        if ($currentRoute == $previousRoute) {
            return;
        }

        $session->set('previous_route', $previousRoute);
        $session->set('current_route', $currentRoute);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }
}
