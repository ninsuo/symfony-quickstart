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
        $key = hash('sha256', $name);

        $item = $menu->addChild($key, array_merge($childParams, [
            'uri'   => $uri,
            'label' => $name,
        ]));

        if ($divider) {
            $menu[$key]->setAttribute('divider', true);
        }

        return $item;
    }

    /**
     * Strategy:
     *
     * A "main" page may directly match (if I just clicked on a link of the menu)
     * Current uri = /admin/users and menu "Manage users" sends to /admin/users
     *
     * A "sub" page may match:
     * Current uri = /admin/users/manage/3, menu that matches is still /admin/users
     *
     * We should keep the longest url that matches (else "/" would match everything).
     *
     * @param ItemInterface $menu
     */
    protected function selectActiveMenu(ItemInterface $menu)
    {
        // We look for the closest menu item that match current uri.
        $currentUri   = $this->container->get('request_stack')->getCurrentRequest()->getRequestUri();
        $length       = 0;
        $active       = null;
        $itemIterator = new \Knp\Menu\Iterator\RecursiveItemIterator($menu);
        $iterator     = new \RecursiveIteratorIterator($itemIterator, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            $compareUri = $item->getUri();
            for ($i = strlen($currentUri); $i != -1; $i--) {
                if (strncmp($currentUri, $compareUri, $i) == 0) {
                    if ($i > $length) {
                        $length = $i;
                        $active = $item;
                    }

                    break ;
                }
            }
        }

        // No match at all (there's the left & right menus, so it often happens)
        if (!$active) {
            return $menu;
        }

        // If current path doesn't match anything, we should prevent "Home" for being highlighted.
        if ($active && $length == 1 && (strlen($currentUri) > 1 || strlen($active->getUri()) > 1)) {
            return $menu;
        }

        // We highlight all submenus until the root node, but except the root node
        $first = null;
        $elem  = $active;
        while (!$elem->isRoot()) {
            $first = $elem;
            $first->setCurrent(true);
            $first->setAttribute('class', $first->getAttribute('class').' active');
            $elem  = $elem->getParent();
        }

        return $menu;
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

    /**
     * This method creates a nested menu from an array of items that can be
     * converted to a tree.
     *
     * $pathCallback will receive $node as argument:
     *
     *  function($node) {
     *      return $node->getPath();
     *  }
     *
     * $addRouteCallback will receive $menu, $key and $value
     *
     *  $this->addRoute($menu, $key, 'route', [
     *    'id' => $value->getId(),
     *    'slug' => $this->slugify($value->getSlug()),
     *  ]);
     *
     * @param $menu             Root menu where to create the nested menu
     * @param $nodes            All nodes that will go to the menu
     * @param $pathCallback     Should transform one node to a string representing path of the node in the menu
     * @param $addRouteCallback Should create a route for a given node
     * @param string $separator Separator of a path (ex: /home/ninsuo/development: "/" is the separator)
     * @param bool $strictMode  Ensures that a node will not overwrite another one
     * @param string|null $trim Trim key and value from undesired characters
     *
     * @see Tree
     */
    protected function createMenuFromTree($menu, $nodes, $pathCallback, $addRouteCallback, $separator = "\n", $strictMode = true, $trim = null)
    {
        $createMenus = function($menu, $node) use (&$createMenus, $addRouteCallback) {
            foreach ($node as $key => $value) {
                if (is_array($value)) {
                    $this->addSubMenu($menu, $key);
                    $createMenus($menu[$key], $value);
                } else {
                    $addRouteCallback($menu, $key, $value);
                }
            }
        };
        $createMenus($menu, Tree::createTree($nodes, $pathCallback, $separator, $strictMode, $trim));
    }

    /**
     * Can be useful if some part of the url could be article titles,
     * or things that could be more seo-friendly.
     *
     * @param string $text
     * @return string
     */
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
