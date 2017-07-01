<?php

namespace BaseBundle\Command;

use BaseBundle\Base\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use BaseBundle\Entity\User;

class UserListCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('user:list')
            ->setDescription('List all users stored on database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->getManager("BaseBundle:User")->findAll();

        $table = new Table($output);

        $table
           ->setHeaders(['User ID', 'Provider', 'Nickname', 'Contact', 'Enabled', 'Admin'])
           ->setRows(array_map(function(User $user) {
               return [
                   $user->getId(),
                   $user->getResourceOwner(),
                   $user->getNickname(),
                   $user->getContact(),
                   var_export($user->isEnabled(), true),
                   var_export($user->isAdmin(), true),
               ];
           }, $users))
           ->render()
        ;

       return 0;
    }
}
