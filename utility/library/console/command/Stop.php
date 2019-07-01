<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-27
 * Time: 23:34
 */

namespace gs\console\command;


use gs\Config;
use Swoole\Process;
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pid_file = Config::getInstance()->get('server.setting.pid_file');
        if (!file_exists($pid_file)) {
            $output->writeln('<error>The pid file does not exists!</error>');
            return false;
        }
        $master_pid = intval(file_get_contents($pid_file));
        //如果进程存在
        if (Process::kill($master_pid, 0)) {
            Process::kill($master_pid);
            $start_time = time();
            while (true) {
                usleep(1000);
                //如果关闭成功
                if (!Process::kill($master_pid, 0)) {
                    //删除pid文件
                    if (file_exists($pid_file)) {
                        @unlink($pid_file);
                    }
                    $output->writeln('<info>Server stop at ' . date('Y-m-d H:i:s') . '</info>');
                    return true;
                }
                if (time() - $start_time > 60) {
                    $output->writeln('<error>Stop server failed.</error>');
                    return false;
                }
            }
        }
        $output->writeln('<error> Server is already stop!</error>');
        return false;
    }
}