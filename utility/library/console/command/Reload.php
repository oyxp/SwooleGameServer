<?php


namespace gs\console\command;


use gs\Config;
use Swoole\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Reload extends Command
{
    public function configure()
    {
        $this->setName('app:reload')
            ->addOption('only_task', 'ot', InputOption::VALUE_OPTIONAL, 'only reload task process?', false)
            ->setDescription('reload server')
            ->setHelp('reload server');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|int|null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $pid_file = Config::getInstance()->get('server.pid_file');
        if (!file_exists($pid_file)) {
            $output->writeln('<error>The pid file does not exists!</error>');
            return false;
        }
        list(, $manager_pid) = explode(',', file_get_contents($pid_file));
        if (!Process::kill($manager_pid, 0)) {
            $output->writeln('<error>The manager process does not exists!</error>');
            return false;
        }
        $signal = $input->getOption('only_task') ? SIGUSR2 : SIGUSR1;
        Process::kill($manager_pid, $signal);
        $output->writeln('<info>Reload server at ' . date('Y-m-d H:i:s') . '</info>');
        return true;
    }
}