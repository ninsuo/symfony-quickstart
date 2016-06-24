<?php

namespace Fuz\QuickStartBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

class DaemonCommand extends ContainerAwareCommand
{
   protected function configure()
    {
        $this
            ->setName('quickstart:daemon')
            ->setDescription('Run a command as a daemon')
            ->addArgument(
                'cmd',
                InputArgument::REQUIRED,
                'Command to run (argv as a json array)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = json_decode($input->getArgument('cmd'), true);
        posix_setsid();
        if (!pcntl_fork()) {
            posix_setsid();
            $builder = new ProcessBuilder($command);
            $builder->setTimeout(10);
            $process = $builder->getProcess();
            $process->disableOutput();
            $process->start();
            try {
                while ($process->isRunning()) {
                    $process->checkTimeout();
                }
            } catch (\Exception $e) {
            }
        }
    }
}