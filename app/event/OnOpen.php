<?php


namespace app\event;


use app\model\AcessTokenModel;
use gs\annotation\Listener;
use gs\Session;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;
use interfaces\SwooleEvent;

/**
 * Class OnOpen
 * @package app\event
 * @Listener(SwooleEvent::ON_OPEN)
 */
class OnOpen implements \interfaces\event\swoole\OnOpen
{

    /**当WebSocket客户端与服务器建立连接并完成握手后会回调此函数
     *
     * 游戏开发中，一般都是通过token来登录服务器，在服务器中获取用户数据并放置的内存（redis）中
     * @param Server $server
     * @param Request $req
     * @return mixed
     */
    public function handle(Server $server, Request $req)
    {
        // TODO: Implement handle() method.
        $request = new \gs\Request($req);
        $access_token = $request->get('access_token');
        if (empty($access_token)) {
            $server->close($request->getFd());
            return false;
        }
        //获取用户信息
        $uid = AcessTokenModel::getUidByAccessToken($access_token);
        if (empty($uid)) {
            $server->close($request->getFd());
            return false;
        }
        //初始化session
        Session::bindUid($uid, $request->getFd());
        //todo 初始化用户信息
    }
}