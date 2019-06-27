<?php


namespace gs;


use traits\Singleton;

class Annotation
{
    use Singleton;
    /**
     * @var array
     */
    protected $definitions = [];
    /**
     * @var array
     */
    protected $scanNamespaces = [
        'app\websocket'
    ];
    /**
     * @var array
     */
    protected $ignoredNames = [
        'Usage',
        'Options',
        'Arguments',
        'Example',
        'package'
    ];

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function initDefinitions()
    {
        foreach ($this->scanNamespaces as $namespace) {
            $dir = str_replace('\\', '/', EASYSWOOLE_ROOT . DIRECTORY_SEPARATOR . $namespace);
            $classes = $this->scanPhpFile($dir, $namespace);
            $this->parseAnnotations($classes);
        }
    }

    /**
     * @param array $classes
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    protected function parseAnnotations(array $classes)
    {
        foreach ($classes as $class) {
            if (!class_exists($class) && !interface_exists($class)) {
                continue;
            }
            // 注解解析器
            $reader = new AnnotationReader();
            $reader = $this->addIgnoredNames($reader);
            $reflectionClass = new \ReflectionClass($class);

            //解析类
            $command = $reader->getClassAnnotation($reflectionClass, Command::class);
            // 没有类注解不解析其它注解
            if (empty($command) || !($command instanceof Command)) {
                continue;
            }
            // 解析方法
//            $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
//            $methods = [];
//            foreach ($publicMethods as $method) {
//                if ($method->isStatic()) {
//                    continue;
//                }
//                // 解析方法注解
//                $sub_command = $reader->getMethodAnnotation($method, SubCommand::class);
//                if (empty($sub_command) || !($sub_command instanceof SubCommand)) {
//                    continue;
//                }
//                $methods[$sub_command->getScmd()] = $method->getName();
//            }
            //当方法不为空时，则存储对应关系
//            if (!empty($methods)) {
            $this->definitions[$command->getCmd()] = [
                'class' => $class,
//                    'method' => $methods,
            ];
//            }
        }
    }

    /**获取注解定义
     * @return array
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * 扫描目录下PHP文件
     *
     * @param string $dir
     * @param string $namespace
     *
     * @return array
     */
    protected function scanPhpFile(string $dir, string $namespace)
    {
        if (!is_dir($dir)) {
            return [];
        }

        $iterator = new \RecursiveDirectoryIterator($dir);
        $files = new \RecursiveIteratorIterator($iterator);

        $phpFiles = [];
        foreach ($files as $file) {
            $fileType = pathinfo($file, PATHINFO_EXTENSION);
            if ($fileType != 'php') {
                continue;
            }
            $replaces = ['', '\\', '', ''];
            $searches = [$dir, '/', '.php', '.PHP'];

            $file = str_replace($searches, $replaces, $file);
            $phpFiles[] = $namespace . $file;
        }
        return $phpFiles;
    }

    /**
     * add ignored names
     *
     * @param AnnotationReader $reader
     *
     * @return AnnotationReader
     */
    protected function addIgnoredNames(AnnotationReader $reader)
    {
        foreach ($this->ignoredNames as $name) {
            $reader->addGlobalIgnoredName($name);
        }
        return $reader;
    }


}