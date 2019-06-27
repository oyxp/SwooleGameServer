<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-27
 * Time: 23:34
 */

namespace gs\console\command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Stop extends Command
{
    protected function configure()
    {
        $this->setName('app:stop')
            ->setDescription('stop app server')
            ->setHelp('stop app server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}