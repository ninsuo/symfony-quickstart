<?php

namespace BaseBundle\EventListener;

use BaseBundle\Base\BaseService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ToggleListener extends BaseService implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        try {
            $currentRoute = $this->get('base.routing.helper')->getCurrentRoute($request);
        } catch (ResourceNotFoundException $ex) {
            return;
        }
        if (is_null($currentRoute)) {
            return;
        }

        if ('hwi_oauth_service_redirect' === $currentRoute['name']) {
            $this->checkService($currentRoute['params']['service']);
        }

        if ('connect' === $currentRoute['name'] && isset($currentRoute['params']['service'])) {
            $this->checkService($currentRoute['params']['service']);
        }
    }

    public function checkService($resourceOwner)
    {
        if (!$this->getParameter("{$resourceOwner}_enabled")) {
            throw new NotFoundHttpException();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 15]],
        ];
    }
}
