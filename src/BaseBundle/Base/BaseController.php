<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidMaxPerPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

abstract class BaseController extends Controller
{
    use ServiceTrait;

    const PAGER_PER_PAGE_LIST    = [10, 25, 50, 100, 250];
    const PAGER_PER_PAGE_DEFAULT = 25;

    public function info($message, array $parameters = [])
    {
        $this->addFlash('info', $this->trans($message, $parameters));
    }

    public function alert($message, array $parameters = [])
    {
        $this->addFlash('alert', $this->trans($message, $parameters));
    }

    public function danger($message, array $parameters = [])
    {
        $this->addFlash('danger', $this->trans($message, $parameters));
    }

    public function success($message, array $parameters = [])
    {
        $this->addFlash('success', $this->trans($message, $parameters));
    }

    public function fwd($controller, array $path = [], array $query = [])
    {
        return $this->forward($controller, $path, $query);
    }

    public function createNamedFormBuilder($name, $type = Type\FormType::class, $data = null, array $options = [])
    {
        return $this->container->get('form.factory')->createNamedBuilder($name, $type, $data, $options);
    }

    public function getPager(Request $request, $data, $prefix = '', $hasJoins = false)
    {
        $adapter = null;
        if ($data instanceof QueryBuilder) {
            $adapter = new DoctrineORMAdapter($data, $hasJoins);
        } elseif (is_array($data)) {
            $adapter = new ArrayAdapter($data);
        } else {
            throw new \RuntimeException('This data type has no Pagerfanta adapter yet.');
        }

        $pager = new Pagerfanta($adapter);
        $pager->setNormalizeOutOfRangePages(true);

        $perPage = $request->query->get($prefix.'per-page', self::PAGER_PER_PAGE_DEFAULT);
        if (!in_array($perPage, self::PAGER_PER_PAGE_LIST)) {
            throw new NotValidMaxPerPageException();
        }

        $pager->setMaxPerPage($perPage);
        $pager->setCurrentPage($request->request->get($prefix.'page') ?: $request->query->get($prefix.'page', 1));

        return $pager;
    }

    public function checkCsrfToken($key, $token)
    {
        if ($token !== $this->get('security.csrf.token_manager')->getToken($key)->getValue()) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }
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
            $route                      = $request->getSession()->get('previous_route');
            $route['params']['_locale'] = $request->getLocale();

            return $this->redirect($this->generateUrl($route['name'], $route['params']));
        }

        $referer = $request->headers->get('referer');
        if (!is_null($referer)) {
            return $this->redirect($referer);
        }

        return $this->redirect($this->generateUrl('home'));
    }
}
