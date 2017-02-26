<?php

namespace BaseBundle\Base;

use BaseBundle\Traits\ServiceTrait;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class BaseMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ServiceTrait;

    const POSITION_LEFT  = 'left';
    const POSITION_RIGHT = 'right';

    public function mainLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, self::POSITION_LEFT);

        return $menu;
    }

    public function mainRightMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, self::POSITION_RIGHT);

        return $menu;
    }

    protected function createMenu(FactoryInterface $factory, $position)
    {
        $menu = $factory->createItem('root');
        if (self::POSITION_LEFT === $position) {
            $menu->setChildrenAttribute('class', $menu->getChildrenAttribute('class').' nav navbar-nav');
        } else {
            $menu->setChildrenAttribute('class', $menu->getChildrenAttribute('class').' nav navbar-nav navbar-right');
        }

        return $menu;
    }

    protected function addRoute(ItemInterface $menu, $name, $route, array $routeParams = [], array $childParams = [], $divider = false)
    {
        $uri = $this->container->get('router')->generate($route, $routeParams);

        return $this->addUri($menu, $name, $uri, $childParams, $divider);
    }

    protected function addUri(ItemInterface $menu, $name, $uri, array $childParams = [], $divider = false)
    {
        $currentUri = $this->container->get('request_stack')->getCurrentRequest()->getRequestUri();

        $key = hash('sha256', $name);

        $item = $menu->addChild($key, array_merge($childParams, [
            'uri'   => $uri,
            'label' => $name,
        ]));

        if ($divider) {
            $menu[$key]->setAttribute('divider', true);
        }

        $isCurrent = false;
        $first     = null;
        $elem      = $item;
        while (!$elem->isRoot()) {
            if (strcmp($currentUri, $elem->getUri()) == 0) {
                $isCurrent = true;
            }
            $first = $elem;
            $elem  = $elem->getParent();
        }

        if (!is_null($first)) {
            if ($isCurrent) {
                $first->setAttribute('class', $first->getAttribute('class').' active');
            }
            $first->setCurrent($isCurrent);
        }

        return $item;
    }

    protected function addSubMenu(ItemInterface $menu, $label)
    {
        $menu->addChild($label, ['uri' => '#']);
        $menu[$label]->setAttribute('class', 'dropdown');
        if ($menu->getParent()) {
            $menu[$label]->setAttribute('class', 'dropdown-submenu');
            $menu[$label]->setExtra('submenu', true);
        }
        $menu[$label]->setChildrenAttribute('class', 'dropdown-menu');
        $menu[$label]->setChildrenAttribute('role', 'menu');
        $menu[$label]->setLinkAttribute('class', 'dropdown-toggle');
        $menu[$label]->setLinkAttribute('data-toggle', 'dropdown');
        $menu[$label]->setLinkAttribute('role', 'button');
        $menu[$label]->setLinkAttribute('aria-expanded', 'false');
    }

    protected function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
