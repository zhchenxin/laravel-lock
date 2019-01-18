<?php

namespace Zhchenxin\Lock;

use Zhchenxin\Lock\Driver\RedisLock;

class LockTool implements LockInterface
{
    /**
     * @var LockInterface
     */
    private $driver;

    public function __construct($name, $config)
    {
        if ($name == 'redis') {
            $this->driver = new RedisLock($config);
        }
    }

    public function lock($key, $requestId, $expire)
    {
        return $this->driver->lock($key, $requestId, $expire);
    }

    public function unlock($key, $requestId)
    {
        return $this->driver->unlock($key, $requestId);
    }

    /**
     * 获取到锁，就执行 $func, 如果没有获取到，则直接返回
     * @param string $key
     * @param int $expire
     * @param callable $func
     * @return bool 获取锁是否成功
     * @throws
     */
    public function serial($key, $expire, Callable $func)
    {
        $requestId = $this->generateRequestId();
        if (!$this->lock($key, $requestId, $expire)){
            return false;
        }

        try {
            $func();
        } finally {
            $this->unlock($key, $requestId);
        }

        return true;
    }

    public function generateRequestId()
    {
        return md5(microtime(true) . mt_rand() . mt_rand());
    }
}