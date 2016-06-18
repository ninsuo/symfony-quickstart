<?php

namespace Fuz\QuickStartBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

/**
 * quickstart.routing
 */
class Routing
{
    protected $router;
    protected $cache = array();

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getCurrentRoute(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $hash = sha1(var_export($pathInfo, true));
        if (in_array($hash, $this->cache)) {
            return $this->cache[$hash];
        }

        $routeParams = $this->router->match($pathInfo);
        $routeName = $routeParams['_route'];
        if (substr($routeName, 0, 1) === '_') {
            return;
        }
        unset($routeParams['_route']);

        $data = array(
            'name' => $routeName,
            'params' => $routeParams,
        );

        $this->cache[$hash] = $data;

        return $data;
    }
}
