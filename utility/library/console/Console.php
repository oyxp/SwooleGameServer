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
use gs\console\command\Stop;
use traits\Singleton;
use gs\console\command\Start;
use Symfony\Component\Console\Application;

class Console
{
    use Singleton;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function run()
    {
        //app init
        App::getInstance()->run();
        //console init
        $application = new Application();
        $application->add(new Start());
        $application->add(new Stop());
        $application->add(new Reload());
        $application->run();
    }
}