<?php


namespace app\http\controller;

use app\App;
use gs\annotation\Route;
use gs\AppException;
use gs\http\HttpController;
use gs\swoole\Task;
use Medoo\Medoo;
use Swoole\Coroutine;

class Index extends HttpController
{
    /**
     * @Route(uri="/",method="GET")
     */
    public function index()
    {
        $db = db();
        mt_srand();
        $ret = $db->insert('user', [
            'id'   => time() + mt_rand(0, 999999),
            'name' => time()
        ]);
        var_dump($db->getPoolStatus());
        return $ret;
    }

    /**
     * @Route(uri="/test_cache",method="GET")
     */
    public function testCache()
    {
        $time = time();
        $cache = cache();
        var_dump($time, spl_object_hash($cache), $cache->set('time', $time));
        $cache = cache();
        var_dump(spl_object_hash($cache), $cache->get('time'));
        var_dump('CID:' . Coroutine::getCid());
        return Coroutine::getCid();
    }

    /**
     * @Route(uri="/t2",method="GET")
     */
    public function testT()
    {
        return db()->transaction(function () {
            db()->insert('user', [
                'id'   => 1,
                'name' => 'test1'
            ]);
            db()->insert('user', [
                'id'   => 2,
                'name' => 'test2'
            ]);

            db()->update('user', [
                'name' => 'test user'
            ], [
                'id' => 1
            ]);
            throw new AppException(1);
        });
    }

    /**
     * @Route(uri="/trans",method="GET")
     * @throws \Exception
     */
    public function testTransaction()
    {
        db()->action(function (Medoo $db) {
            $db->insert('user', [
                'id'   => 2,
                'name' => 'test'
            ]);

            $db->update('user', [
                'name' => 'test user'
            ], [
                'id' => 1
            ]);
            return true;
        });

        return [
            '1' => db()->get('user', '*', [
                'id' => 1
            ]),
            '2' => db()->get('user', '*', [
                'id' => 2
            ]),
        ];
    }

    /**
     * @return bool
     * @throws \Exception
     * @Route(uri="/test_for_update",method="GET")
     */
    public function testSelectForUpdate()
    {
        $ret = db()->action(function (Medoo $db) {
            $data = $db->get('user', '*', Medoo::raw('WHERE id = 1 FOR UPDATE'));
            sleep(10);
            $db->update('user', [
                'name' => 'snailZED'
            ], ['id' => $data['id']]);
            return true;
        });
        return $ret;
    }


    /**
     * @Route(uri="/task",method="GET")
     */
    public function task()
    {
        return Task::async('test', ['HI']);
    }

    /**
     * @Route(uri="/cache" ,method="GET")
     */
    public function getCacheStat()
    {
        return [
            'status' => cache()->getPoolStatus(),
            'workId' => App::$swooleServer->worker_pid,
            'data'   => cache()->keys('*'),

        ];
    }

    /**
     * @Route(uri="/db",method="GET")
     * @return array
     *
     */
    public function getDbStat()
    {
        return [
            'status' => db()->getPoolStatus(),
            'workId' => App::$swooleServer->worker_pid,
        ];
    }
}