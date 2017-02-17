<?php

namespace BaseBundle\EventListener;

use BaseBundle\Base\BaseService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class IncompleteProfileListener extends BaseService implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $token = $this->get('security.token_storage')->getToken();
        if (is_null($token)) {
            return;
        }

        $user = $token->getUser();
        if (is_scalar($user)) {
            return;
        }

        $services = $this->get('base.incomplete_profile.service.provider')->getIncompleteProfileServices();

        $route = $request->get('_route');
        if (is_null($route)) {
            return;
        }

        foreach ($services as $service) {
            if ($service->getRoute() === $route) {
                return;
            }
        }

        foreach ($services as $service) {
            if (!$service->isProfileComplete($user)) {
                $event->setResponse(new RedirectResponse(
                    $this->get('router')->generate($service->getRoute())
                ));

                return;
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 5)),
        );
    }
}
