<?php

namespace BaseBundle\Base;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

abstract class BaseController extends Controller
{
    /**
     * Symfony's var_dump.
     *
     * @param mixed $var
     */
    protected function dump($var)
    {
        VarDumper::dump($var);
    }

    /**
     * This method sends user back to the last url he comes from.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function goBack(Request $request)
    {
        if ($request->getSession()->has('previous_route')) {
            $route = $request->getSession()->get('previous_route');
            $route['params']['_locale'] = $request->getLocale();

            return $this->redirect($this->generateUrl($route['name'], $route['params']));
        }

        $referer = $request->headers->get('referer');
        if (!is_null($referer)) {
            return $this->redirect($referer);
        }

        return $this->redirect($this->generateUrl('home'));
    }

    public function info($message, array $parameters = array())
    {
        $this->addFlash('info', $this->trans($message, $parameters));
    }

    public function alert($message, array $parameters = array())
    {
        $this->addFlash('alert', $this->trans($message, $parameters));
    }

    public function danger($message, array $parameters = array())
    {
        $this->addFlash('danger', $this->trans($message, $parameters));
    }

    public function success($message, array $parameters = array())
    {
        $this->addFlash('success', $this->trans($message, $parameters));
    }

    public function trans($property, array $parameters = array())
    {
        return $this->container->get('translator')->trans($property, $parameters);
    }

    public function fwd($controller, array $path = array(), array $query = array())
    {
        return $this->forward($controller, $path, $query);
    }
}
