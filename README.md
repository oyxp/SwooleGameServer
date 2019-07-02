## 常驻内存框架实现逻辑

- 1、命令行参数解析（command parse）
> 就是解析$argv这个全局参数

  
- 2、初始化设置：如日志目录、临时目录以及pid存放目录
> is_dir() 
> pathinfo()  
> file_exists()  
> mkdir()

- 3、注册全局的错误处理函数、异常处理函数和应用关闭函数
> set_error_handle()
> set_exception_handle()
> register_shutdown_function()


- 4、根据命令行传入的参数，启动server
> 加载对应的配置文件，启动server，注册相关事件回调函数

- 5、添加自定义进程
 
- 6、组件开发：如redis连接池，数据库连接池、工具类
 
## Swoole Game Server
> swoole websocket server
  
## 启动

     php bin/server app:start
     php bin/server app:stop
     php bin/server app:reload
     php bin/server app:status

## 通信格式(传输二进制，需安装`msgpack`拓展)

    {"c":"命令，注解定义" , "d":{请求参数}}
    
    {"c":1 ,"d":{"id":1}}