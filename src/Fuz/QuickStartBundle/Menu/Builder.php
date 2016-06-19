<?php

namespace Fuz\QuickStartBundle\Menu;

use Fuz\QuickStartBundle\Base\BaseMenu;
use Knp\Menu\FactoryInterface;

class Builder extends BaseMenu
{
    public function mainLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);
        $this->addRoute($menu, 'quickstart.menu.home', 'home');

        return $menu;
    }

    public function userLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);
        $this->addRoute($menu, 'quickstart.menu.home', 'home');

        return $menu;
    }

    public function mainRightMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_RIGHT);

        return $menu;
    }

    public function userRightMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_RIGHT);

        return $menu;
    }
}
