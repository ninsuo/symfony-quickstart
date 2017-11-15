<?php

namespace AdminBundle\Twig\Extension;

use BaseBundle\Base\BaseTwigExtension;

class SettingsExtension extends BaseTwigExtension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('settings', [$this, 'settings']),
        ];
    }

    public function settings($property, $default = null)
    {
        if (isset($this->settings[$property])) {
            return $this->settings[$property];
        }

        $value = $this->getManager('AdminBundle:Setting')->get($property, $default);
        $this->settings[$property] = $value;

        return $value;
    }

    public function getName()
    {
        return 'settings';
    }
}