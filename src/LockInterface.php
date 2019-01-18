<?php

namespace Zhchenxin\Lock;

interface LockInterface
{
    public function lock($key, $requestId, $expire);

    public function unlock($key, $requestId);
}