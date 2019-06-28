## Swoole H5 Game Server

## 启动

     php server app:start

## 通信格式(传输二进制，需安装`msgpack`拓展)

    {"c":"命令，注解定义" , "d":{请求参数}}
    
    {"c":1 ,"d":{"id":1}}