<?php

namespace BaseBundle\Twig\Extension;

class LightExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('http_build_query', 'http_build_query', ['is_safe' => ['html', 'html_attr']]),
            new \Twig_SimpleFunction('array_to_query_fields', [$this, 'arrayToQueryFields'], ['is_safe' => ['html']]),
        ];
    }

    public function arrayToQueryFields($key, $value, $keyPrefix = null)
    {
        $currentKey = $keyPrefix ? $keyPrefix.'['.$key.']' : $key;

        if (is_string($value)) {
            return '<input type="hidden" name="'.htmlentities($currentKey).'" value="'.htmlentities($value).'"/>';
        }

        $inputs = '';
        foreach ($value as $childKey => $childValue) {
            $inputs .= $this->arrayToQueryInputs($childKey, $childValue, $currentKey);
        }

        return $inputs;
    }

    public function getName()
    {
        return 'light';
    }
}
