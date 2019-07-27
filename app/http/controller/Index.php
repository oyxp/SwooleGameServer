<?php


namespace app\http\controller;

use app\App;
use gs\annotation\Route;
use gs\http\HttpController;
use gs\swoole\Task;
use Medoo\Medoo;

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
        return $ret;
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
     * @Route(uri="/cache",method="GET")
     */
    public function testCache()
    {
        var_dump(App::$swooleServer->worker_id);
        var_dump('cache hash:' . spl_object_hash(cache()));
        return 'cache hash:' . (cache()->set('time', time()));
    }

    /**
     * @Route(uri="/task",method="GET")
     */
    public function task()
    {
        return Task::async('test', ['HI']);
    }
}