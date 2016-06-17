<?php

namespace Fuz\QuickStartBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Fuz\QuickStartBundle\Base\BaseService;

/**
 * This class throws 404 exceptions when a requested
 * service has been disabled in application's
 * parameters.
 */
class ToggleListener extends BaseService implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        try {
            $currentRoute = $this->get('quickstart.routing')->getCurrentRoute($request);
        } catch (ResourceNotFoundException $ex) {
            return;
        }
        if (is_null($currentRoute)) {
            return;
        }

        if (false !== strpos($currentRoute['name'], 'fos_') && !$this->getParameter('login_form')) {
            throw new NotFoundHttpException();
        }

        if ('connect' === $currentRoute['name'] && isset($currentRoute['params']['service'])) {
            $this->checkService($currentRoute['params']['service']);
        }

        if ('_login' === substr($currentRoute['name'], -6)) {
            $this->checkService(substr($currentRoute['name'], 0, -6));
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
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 15)),
        );
    }
}
