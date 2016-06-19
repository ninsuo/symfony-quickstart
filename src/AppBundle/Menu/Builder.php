<?php

namespace AppBundle\Menu;

use Fuz\QuickStartBundle\Menu\Builder as DemoBuilder;
use Knp\Menu\FactoryInterface;

class Builder extends DemoBuilder
{
   public function mainLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);
        $this->addRoute($menu, 'quickstart.menu.home', 'home');

        /*
          $this->addSubMenu($menu, 'test');
          $this->addRoute($menu['test'], 'testA', 'testa');
          $this->addRoute($menu['test'], 'testB', 'testb', array(), array(), true);
          $this->addRoute($menu['test'], 'testC', 'testc');
        */

        return $menu;
    }
}
