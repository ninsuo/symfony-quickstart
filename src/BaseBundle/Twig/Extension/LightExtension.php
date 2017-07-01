<?php

namespace BaseBundle\Twig\Extension;

use BaseBundle\Base\BaseTwigExtension;

class LightExtension extends BaseTwigExtension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('http_build_query', 'http_build_query', ['is_safe' => ['html', 'html_attr']]),
            new \Twig_SimpleFunction('absolute_url', [$this, 'absoluteUrl']),
            new \Twig_SimpleFunction('current_route', [$this, 'currentRoute']),
            new \Twig_SimpleFunction('current_locale', [$this, 'currentLocale']),
            new \Twig_SimpleFunction('current_uri', [$this, 'currentUri']),
            new \Twig_SimpleFunction('page_id', [$this, 'pageId']),
            new \Twig_SimpleFunction('array_to_query_fields', [$this, 'arrayToQueryFields'], ['is_safe' => ['html']]),
        ];
    }

    public function absoluteUrl($asset = '')
    {
        $request = $this->get('request_stack')->getMasterRequest();
        $baseurl = $request->getScheme().'://'.$request->getHttpHost().$request->getBasePath();

        return $baseurl.$asset;
    }

    public function currentRoute()
    {
        return $this->get('base.routing.helper')->getCurrentRoute();
    }

    public function currentLocale()
    {
        return $this->get('request_stack')->getMasterRequest()->getLocale();
    }

    public function currentUri()
    {
        return $this->get('request_stack')->getMasterRequest()->getUri();
    }

    public function pageId($hashed = false, $ignoredParams = [])
    {
        $request = $this->get('request_stack')->getMasterRequest();
        $route = $request->get('_route');
        $params = $request->get('_route_params');

        unset($params['_locale']);
        foreach ($ignoredParams as $ignoredParam) {
            unset($params[$ignoredParam]);
        }

        $data = $route . '/' . join('/', $params);

        if ($hashed) {
            return strtr(base64_encode(hex2bin(hash('sha256', $data))), '+/=', '-_,');
        }

        return $data;
    }

    /**
     * Used to keep arguments of the query string when generating a new form with method GET.
     * See: BaseBundle::macros.html.twig
     *
     * @param string $key
     * @param mixed $value
     * @param string $keyPrefix
     * @return string
     */
    public function arrayToQueryFields($key, $value, $keyPrefix = null)
    {
        $currentKey = $keyPrefix ? $keyPrefix.'['.$key.']' : $key;

        if (is_string($value)) {
            return '<input type="hidden" name="'.htmlentities($currentKey).'" value="'.htmlentities($value).'"/>';
        }

        $inputs = '';
        foreach ($value as $childKey => $childValue) {
            $inputs .= $this->arrayToQueryFields($childKey, $childValue, $currentKey);
        }

        return $inputs;
    }

    public function getName()
    {
        return 'light';
    }
}
