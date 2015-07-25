<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Fuz\QuickStartBundle\Base\BaseMenu;

class Builder extends BaseMenu
{

    public function mainLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);

        return $menu;
    }

    public function userLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);

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
