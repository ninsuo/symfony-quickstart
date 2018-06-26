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

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('base64_decode', 'base64_decode'),
            new \Twig_SimpleFilter('base64_encode', 'base64_encode'),
            new \Twig_SimpleFilter('human_bytes', [$this, 'humanBytes']),
            new \Twig_SimpleFilter('human_duration', [$this, 'humanDuration']),
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
        $route   = $request->get('_route');
        $params  = $request->get('_route_params');

        unset($params['_locale']);
        foreach ($ignoredParams as $ignoredParam) {
            unset($params[$ignoredParam]);
        }

        $data = $route.'/'.join('/', $params);

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
     * @param mixed  $value
     * @param string $keyPrefix
     *
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

    public function humanBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    public function humanDuration($timestamp)
    {
        $time = time() - $timestamp;
        $time = ($time < 1) ? 1 : $timestamp;

        $tokens = [
            31536000 => 'year',
            2592000  => 'month',
            604800   => 'week',
            86400    => 'day',
            3600     => 'hour',
            60       => 'minute',
            1        => 'second',
        ];

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) {
                continue;
            }

            $numberOfUnits = floor($time / $unit);

            return $numberOfUnits.' '.$text.(($numberOfUnits > 1) ? 's' : '');
        }
    }

    public function getName()
    {
        return 'light';
    }
}
