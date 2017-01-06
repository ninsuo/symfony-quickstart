<?php

namespace BaseBundle\Base;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
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

    public function createNamedFormBuilder($name, $type = Type\FormType::class, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->createNamedBuilder($name, $type, $data, $options);
    }

    public function persistHandleDuplicates(FormInterface $form, $entity, $entry)
    {
        $em = $this
           ->get('doctrine')
           ->getManager()
        ;

        try {
            $em->persist($entity);
            $em->flush();
        } catch (UniqueConstraintViolationException $ex) {
            $form->addError(new FormError(
               $this->get('translator')->trans('base.form.duplicate_error', [
                   '%entry%' => $entry,
               ])
            ));
        }
    }

    public function getManager($manager)
    {
        return $this
           ->get('doctrine')
           ->getManager()
           ->getRepository($manager)
        ;
    }
}
