<?php

namespace BaseBundle\Twig\Extension;

use BaseBundle\Base\BaseTwigExtension;

class SettingExtension extends BaseTwigExtension
{
    protected $settings = [];

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('setting', [$this, 'setting']),
        ];
    }

    public function setting($property, $default = null)
    {
        if (isset($this->settings[$property])) {
            return $this->settings[$property];
        }

        $value                     = $this->getManager('BaseBundle:Setting')->get($property, $default);
        $this->settings[$property] = $value;

        return $value;
    }

    public function getName()
    {
        return 'setting';
    }
}
