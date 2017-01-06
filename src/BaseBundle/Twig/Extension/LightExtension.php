<?php

namespace BaseBundle\Twig\Extension;

class LightExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('array_to_query_inputs', [$this, 'arrayToQueryInputs'], ['is_safe' => ['html']]),
        ];
    }

    public function arrayToQueryInputs($key, $value, $keyPrefix = null)
    {
        $currentKey = $keyPrefix ? $keyPrefix . '[' . $key . ']' : $key;

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