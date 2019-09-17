<?php


namespace gs;


use gs\helper\StringHelper;
use gs\helper\TimeHelper;

/**
 * Class Model
 * @package gs
 */
class Model
{
    /**
     * 缓存所有表名
     * @var array
     */
    public static $tables = [];

    /**
     * @return string
     */
    public static function getTable()
    {
        if (!isset(self::$tables[static::class])) {
            self::$tables[static::class] = StringHelper::snake(substr(static::class, strrpos(static::class, '\\') + 1));
        }
        return self::$tables[static::class];
    }


    /**
     * @param $datas
     * @return bool|\PDOStatement
     */
    public static function insert($datas)
    {
        $datas['insert_time'] = TimeHelper::getMilliSecond();
        return db()->insert(self::getTable(), $datas);
    }

    /**
     * @param $data
     * @param null $where
     * @return bool|\PDOStatement
     */
    public static function update($data, $where = null)
    {
        $data['update_time'] = TimeHelper::getMilliSecond();
        return db()->update(self::getTable(), $data, $where);
    }

    /**
     * @param $join
     * @param null $columns
     * @param null $where
     * @return array|bool
     */
    public static function select($join, $columns = null, $where = null)
    {
        return db()->select(self::getTable(), $join, $columns, $where);
    }


    /**
     * @param $where
     * @return bool|\PDOStatement
     */
    public static function delete($where)
    {
        return db()->delete(self::getTable(), $where);
    }

    /**
     * @param $columns
     * @param null $where
     * @return bool|\PDOStatement
     */
    public static function replace($columns, $where = null)
    {
        return db()->replace(self::getTable(), $columns, $where);
    }

    /**
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return array|mixed
     */
    public static function get($join = null, $columns = null, $where = null)
    {
        return db()->get(self::getTable(), $join, $columns, $where);
    }

    /**
     * @param $join
     * @param null $where
     * @return bool
     */
    public static function has($join, $where = null)
    {
        return db()->has(self::getTable(), $join, $where);
    }

    /**
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return array|bool
     */
    public static function rand($join = null, $columns = null, $where = null)
    {
        return db()->rand(self::getTable(), $join, $columns, $where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool|int|mixed|string
     */
    public static function count($join = null, $column = null, $where = null)
    {
        return db()->count(self::getTable(), $join, $column, $where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool|int|mixed|string
     */
    public static function max($join = null, $column = null, $where = null)
    {
        return db()->max(self::getTable(), $join, $column, $where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool|int|mixed|string
     */
    public static function avg($join = null, $column = null, $where = null)
    {
        return db()->avg(self::getTable(), $join, $column, $where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool|int|mixed|string
     */
    public static function sum($join = null, $column = null, $where = null)
    {
        return db()->sum(self::getTable(), $join, $column, $where);
    }

    /**返回一列数据  [0=>data0,1=>data1]  只能选择一列
     * @param $join
     * @param null $columns
     * @param null $where
     * @return array|bool
     */
    public static function column($join, $columns = null, $where = null)
    {
        return db()->select(self::getTable(), $join, $columns, $where);
    }

    /**返回一条记录中的某个字段值，只能选择一个字段
     * @param null $join
     * @param null $columns
     * @param null $where
     * @return array|mixed
     */
    public static function value($join = null, $columns = null, $where = null)
    {
        return db()->get(self::getTable(), $join, $columns, $where);
    }

    /**
     * @param $query
     * @param array $map
     * @return bool|\PDOStatement
     */
    public static function query($query, $map = [])
    {
        return db()->query($query, $map);
    }
}