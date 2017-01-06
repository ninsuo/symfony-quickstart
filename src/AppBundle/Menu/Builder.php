<?php

namespace AppBundle\Menu;

use BaseBundle\Base\BaseMenu;
use Knp\Menu\FactoryInterface;

class Builder extends BaseMenu
{
   public function mainLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);
        $this->addRoute($menu, 'base.menu.home', 'home');

        /*
          See the parent class if you want to implement right menus

          $this->addSubMenu($menu, 'test');
          $this->addRoute($menu['test'], 'testA', 'testa');
          $this->addRoute($menu['test'], 'testB', 'testb', array(), array(), true);
          $this->addRoute($menu['test'], 'testC', 'testc');
        */

        return $menu;
    }

    public function userLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);
        $this->addRoute($menu, 'base.menu.home', 'home');

        return $menu;
    }
}
