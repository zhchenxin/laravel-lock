<?php

namespace Zhchenxin\Lock;

use Zhchenxin\Lock\Driver\RedisLock;

class LockTool
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

    /**
     * 获取锁
     * @param string $key 锁名称
     * @param string $requestId 一个随机数, 用于释放锁
     * @param int $expire 过期时间,单位秒
     * @return bool 是否获取到锁
     */
    public function lock($key, $requestId, $expire)
    {
        return $this->driver->lock($key, $requestId, $expire);
    }

    /**
     * 释放锁
     * @param string $key 锁名称
     * @param string $requestId 一个随机数, 与获取锁时的参数相同
     * @return bool 是否释放成功
     */
    public function unlock($key, $requestId)
    {
        return $this->driver->unlock($key, $requestId);
    }

    /**
     * 获取到锁，就执行 $func, 如果没有获取到，则直接返回
     * @param string $key 锁名称
     * @param int $expire 超时时间, 最好设置成业务处理的最大处理时间
     * @param callable $func 业务处理
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

    /**
     * 获取到锁，就执行 $func, 如果没有获取到，等待0.1秒, 然后重新去获取锁
     * @param $key
     * @param $expire
     * @param callable $func
     */
    public function queue($key, $expire, Callable $func)
    {
        while (true) {
            $requestId = $this->generateRequestId();

            if ($this->lock($key, $requestId, $expire)){
                try {
                    $func();
                } finally {
                    $this->unlock($key, $requestId);
                }
                return;
            } else {
                usleep(1000);
            }
        }
    }

    public function generateRequestId()
    {
        return md5(microtime(true) . mt_rand() . mt_rand());
    }
}