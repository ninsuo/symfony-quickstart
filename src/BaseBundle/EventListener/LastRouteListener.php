<?php

namespace BaseBundle\EventListener;

use BaseBundle\Services\RoutingHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LastRouteListener implements EventSubscriberInterface
{
    protected $routingHelper;

    public function __construct(RoutingHelper $routingHelper)
    {
        $this->routingHelper = $routingHelper;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $currentRoute = $this->routingHelper->getCurrentRoute($request);
        if (!$currentRoute) {
            return;
        }

        $session       = $request->getSession();
        $previousRoute = $session->get('current_route', []);
        if ($currentRoute == $previousRoute) {
            return;
        }

        $session->set('previous_route', $previousRoute);
        $session->set('current_route', $currentRoute);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 15]],
        ];
    }
}
