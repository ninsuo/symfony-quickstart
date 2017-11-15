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

        return $this->selectActiveMenu($menu);
    }

    public function mainRightMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_RIGHT);

        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addSubMenu($menu, 'base.menu.admin.main');
            $this->addRoute($menu['base.menu.admin.main'], 'base.menu.admin.users', 'admin_users');
            $this->addRoute($menu['base.menu.admin.main'], 'base.menu.admin.groups', 'admin_groups');
            $this->addRoute($menu['base.menu.admin.main'], 'base.menu.admin.settings', 'admin_settings');
        }

        return $this->selectActiveMenu($menu);
    }
}
