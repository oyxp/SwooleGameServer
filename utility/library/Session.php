<?php


namespace gs;


class Session
{
    /**存放 fd => uid 映射关系
     * @var array
     */
    private static $fd_to_uid = [];

    /**存放uid => fd的映射关系
     * @var array
     */
    private static $uid_to_fd = [];

    /**
     * @param string $uid
     * @return mixed|null
     */
    public static function getFdByUid(string $uid)
    {
        return self::$uid_to_fd[$uid] ?? null;
    }

    /**
     * @param int $fd
     * @return mixed|null
     */
    public static function getUidByFd(int $fd)
    {
        return self::$fd_to_uid[$fd] ?? null;
    }

    /**
     * @param $uid
     * @param $fd
     */
    public static function bindUid($uid, $fd)
    {
        self::$uid_to_fd[$uid] = $fd;
        self::$fd_to_uid[$fd] = $uid;
    }

    /**
     * @param $uid
     */
    public static function closeUid($uid)
    {
        $fd = self::getFdByUid($uid);
        unset(self::$fd_to_uid[$fd]);
        unset(self::$uid_to_fd[$uid]);
    }
}