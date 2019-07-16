<?php

namespace Zhchenxin\Lock;

interface LockInterface
{
    /**
     * 获取锁
     * @param string $key         锁名称
     * @param string $requestId   锁id
     * @param int $expire         超时时间，单位秒
     * @return bool 是否获取到锁
     */
    public function lock($key, $requestId, $expire);

    /**
     * 释放锁
     * @param string $key        锁名称
     * @param string $requestId  锁id
     */
    public function unlock($key, $requestId);
}