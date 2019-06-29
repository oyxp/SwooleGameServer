<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-29
 * Time: 22:58
 */

namespace gs\console\command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Status extends Command
{
    public function configure()
    {
        $this->setName('app:status')
            ->setDescription('app status')
            ->setHelp('app status');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        return 'status';
    }
}