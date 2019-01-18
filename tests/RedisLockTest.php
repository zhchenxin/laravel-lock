<?php

namespace Zhchenxin\Lock\Tests;

use PHPUnit\Framework\TestCase;
use Zhchenxin\Lock\Driver\RedisLock;

class RedisLockTest extends TestCase
{
    /**
     * @var RedisLock
     */
    protected $redisLock;

    public function setUp()
    {
        parent::setUp();
        $this->redisLock = new RedisLock();
    }

    /**
     * 正常流程
     * @test
     */
    public function it_should_work()
    {
        $key = 'it_should_work';
        $request_id = '1';

        $ret = $this->redisLock->lock($key, $request_id, 60);
        $this->assertTrue($ret, '第一次获取成功');

        $ret = $this->redisLock->lock($key, $request_id, 60);
        $this->assertFalse($ret, '第二次获取锁失败');

        $ret = $this->redisLock->unlock($key, '2');
        $this->assertFalse($ret, 'id错误，释放锁失败');

        $ret = $this->redisLock->unlock($key, $request_id);
        $this->assertTrue($ret, '释放锁成功');
    }

    /**
     * 超时之后，可以重新获取锁
     * @test
     */
    public function it_should_unlock_while_timeout()
    {
        $key = 'it_should_unlock_while_timeout';
        $request_id = '1';

        $ret = $this->redisLock->lock($key, $request_id, 1);
        $this->assertTrue($ret, '第一次获取成功');

        sleep(2);

        $ret = $this->redisLock->lock($key, $request_id, 1);
        $this->assertTrue($ret, '第二次获取成功');
    }
}