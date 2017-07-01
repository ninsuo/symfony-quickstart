<?php

namespace BaseBundle\Command;

use BaseBundle\Base\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserAdminCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('user:admin')
            ->setDescription('Set/Unset user as admin')
            ->addArgument('id', InputArgument::REQUIRED, 'User id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $user = $this->getManager("BaseBundle:User")->find($id);

        if (is_null($user)) {
            $output->writeln("<error>User {$id} not found.</error>");

            return 1;
        }

        $user->setIsAdmin(1 - $user->isAdmin());
        $this->getManager()->persist($user);
        $this->getManager()->flush();

        $status = $user->isAdmin() ? '<question>admin</question>' : '<error>simple user</error>';
        $output->writeln("User <info>{$id}</info> is now: {$status}.");

        return 0;
    }
}
