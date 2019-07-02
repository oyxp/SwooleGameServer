<?php
/**
 * Created by PhpStorm.
 * User: snailzed
 * Date: 2019-06-27
 * Time: 23:32
 */

namespace gs\console;

use app\App;
use gs\console\command\Reload;
use gs\console\command\Status;
use gs\console\command\Stop;
use traits\Singleton;
use gs\console\command\Start;
use Symfony\Component\Console\Application;

class Console
{
    use Singleton;

    private $application;


    private function __construct()
    {
        //console init
        $this->application = new Application();
        $this->application->add(new Start());
        $this->application->add(new Stop());
        $this->application->add(new Reload());
        $this->application->add(new Status());
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function run()
    {
        global $argv;
        if (count($argv) > 1 && !in_array($argv[1], ['list', 'help'])) {
            //app init
            App::getInstance()->run();
        }
        $this->application->run();
    }
}