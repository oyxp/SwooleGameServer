<?php


namespace gs;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use gs\annotation\Command;
use gs\annotation\Listener;
use gs\annotation\Process;
use gs\helper\ComposerHelper;
use interfaces\event\CustomEvent;
use interfaces\event\SwooleEvent;
use traits\Singleton;

class Annotation
{
    use Singleton;
    /**
     * @var array
     */
    private $definitions = [];
    /**
     * @var array
     */
    protected $scanNamespaces = [
        'app\\websocket',
        'app\\event',
        'app\\task',
        'app\\process',
        'app\\http',
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

    public function __construct()
    {
        //这里进行命令扫描和解析
        AnnotationRegistry::registerLoader(function ($class) {
            if (class_exists($class) || interface_exists($class)) {
                return true;
            }
            return false;
        });
        $this->scanNamespaces = array_unique(array_merge($this->scanNamespaces, Config::getInstance()->get('scan_namespace')));
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function collectDefinitions()
    {
        foreach ($this->scanNamespaces as $namespace) {
            $dir = realpath(str_replace('\\', '/', ComposerHelper::getDirByNamespace($namespace)));
            if (false === $dir) {
                continue;
            }
            $classes = $this->scanPhpFile($dir, $namespace);
            $this->parseAnnotations($classes);
        }
        var_dump($this->definitions);
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

            //解析类名
            $class_annos = $reader->getClassAnnotations($reflectionClass);
            if (!empty($class_annos)) {
                foreach ($class_annos as $anno) {
                    //事件监听
                    if ($anno instanceof Listener) {
                        if ($reflectionClass->implementsInterface(SwooleEvent::class)) {
                            $this->definitions['swoole_event'][$anno->getEvent()] = $reflectionClass->getName();
                        } else if ($reflectionClass->implementsInterface(CustomEvent::class)) {
                            $this->definitions['custom_event'][$anno->getEvent()][] = $reflectionClass->getName();
                        }
                    } else if ($anno instanceof Process) {
                        //自定义进程
                        $this->definitions['process'][] = [
                            'class' => $reflectionClass->getName(),
                            'name'  => $anno->getName(),
                        ];
                    }
                }
            }
            // 解析方法
            $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($publicMethods as $method) {
                if ($method->isStatic()) {
                    continue;
                }
                // 解析方法注解
                $sub_command = $reader->getMethodAnnotation($method, Command::class);
                if (empty($sub_command) || !($sub_command instanceof Command)) {
                    continue;
                }
                $this->definitions['command'][$sub_command->getCode()] = [
                    'class'  => $method->getDeclaringClass()->getName(),
                    'method' => $method->getName(),
                ];
            }
        }
    }

    /**获取注解定义
     * @return mixed
     */
    public function getDefinitions($name = null)
    {
        if (!is_null($name)) {
            if (!strpos($name, '.')) {
                return $this->definitions[$name] ?? false;
            }
            $value = explode('.', $name);
            $definitions = $this->definitions;
            while ($key = array_shift($value)) {
                if (isset($definitions[$key])) {
                    $definitions = $definitions[$key];
                } else {
                    return [];
                }
            }
            return $definitions;
        }
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