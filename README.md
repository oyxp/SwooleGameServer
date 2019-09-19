## 常驻内存框架实现逻辑

- 1、命令行参数解析（command parse）
> 就是解析$argv这个全局参数,app:start,app:stop,app:reload,app:status

  
- 2、初始化设置：如日志目录、临时目录以及pid存放目录
> is_dir() 
> pathinfo()  
> file_exists()  
> mkdir()

- 3、注册全局的错误处理函数、异常处理函数和应用关闭函数、日志处理、配置文件处理
> set_error_handle()
> set_exception_handle()
> register_shutdown_function()


- 4、解析配置文件，注册相关回调函数
> 加载对应的配置文件，注册相关事件回调函数

- 5、添加自定义进程
> 启动server前，添加自定义进程

- 6、添加自定义任务
> 任务可以是匿名函数或者一个类

- 7、启动|关闭|重启 server
> php bin/server app:start 

> php bin/server app:stop 

> php bin/server app:reload 

> php bin/server app:status 

- 8、组件开发：如连接池、配置解析类、http组件库（验证）、自定义session
 
## Swoole Game Server
> swoole websocket server
  
## 命令行

    启动： php bin/server app:start
    停止： php bin/server app:stop
    重启： php bin/server app:reload
    todo： php bin/server app:status

## websocket通信格式（server.php可配置打包解包函数,支持json和msgpack）

    发送命令：{"c":"命令，注解定义" , "d":{请求参数}}
    eg：{"c":1 ,"d":{"id":1}}
    
    websocket类需继承 |gs\WebsocketController,可以使用 `$this->request->getParam()`获取参数，使用 $this->getUid()获取绑定的UID
    
## 特性
   
    1、协程http请求
    2、协程webscoket
    3、异步、同步 单|多task，支持闭包
    4、自定义进程
    5、redis、db连接池 
    6、多语种
    7、注解路由、注解task、注解进程、注解websocket命令
    
    
## task组件

- gs\swoole\Task::async($name, array $data, int $taskWorkerId = -1)
   
   
     异步任务，必须设置onfinish回调,返回task_worker_id
     $name: 可以是定义的task任务名，使用Task注解的name属性定义;也可以是一个闭包函数
     $data: 调用任务类的handle方法的参数列表；调用闭包的参数列表
     $taskWorkerId： 指定到哪个task worker执行

- gs\swoole\Task::sync($name, array $data, $timeout = 0.5, int $taskWorkerId = -1)


     同步任务，必须设置onfinish回调,返回task_worker_id
     $name: 可以是定义的task任务名，使用Task注解的name属性定义;也可以是一个闭包函数
     $data: 调用任务类的handle方法的参数列表；调用闭包的参数列表
     $taskWorkerId： 指定到哪个task worker执行
          
- gs\swoole\Task::asyncMulti(array $tasks, $timeout = 0.5)
      
     
      并发执行多个task异步任务
      $name:  [ ['任务名|匿名函数', [参数列表] ]
              必须是一个数组，数组有且只有两个元素，第一个元素必须为任务名或者匿名函数，第二个元素必须为参数列表数组
      
- gs\swoole\Task::coMulti(array $tasks, $timeout = 0.5)
       
      
      并发执行Task并进行协程调度 ，任务完成或超时，返回结果数组。结果数组中每个任务结果的顺序与$tasks对应，某个任务执行失败或超时，对应的结果数组项为false
      $name:  [ ['任务名|匿名函数', [参数列表] ]
              必须是一个数组，每个元素是一个数组，每个数组有且只有两个元素，第一个元素必须为任务名或者匿名函数，第二个元素必须为参数列表数组


使用注解定义task

 ```php
  <?php
  
  
  namespace app\task;
  
  
  use gs\annotation\Task;
  
  /**
   * Class Test
   * @package app\task
   * @Task(name="test")
   */
  class Test
  {
      //必须要有handle方法
      public static function handle($hi)
      {
          var_dump($hi);
      }
  } 

 ```
