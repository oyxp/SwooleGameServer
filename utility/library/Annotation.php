<?php


namespace gs;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use gs\annotation\Command;
use gs\helper\ComposerHelper;
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

    public function __construct()
    {
        //这里进行命令扫描和解析
        AnnotationRegistry::registerLoader(function ($class) {
            if (class_exists($class) || interface_exists($class)) {
                return true;
            }
            return false;
        });
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
                $this->definitions[$sub_command->getCode()] = [
                    'class'  => $method->getDeclaringClass()->getName(),
                    'method' => $method->getName(),
                ];
            }
        }
    }

    /**获取注解定义
     * @return array
     */
    public function getDefinitions($code = null)
    {
        if (!is_null($code)) {
            return $this->definitions[$code] ?? false;
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