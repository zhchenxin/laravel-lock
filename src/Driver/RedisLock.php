<?php

namespace Zhchenxin\Lock\Driver;

use Predis\Client;
use Zhchenxin\Lock\LockInterface;

class RedisLock implements LockInterface
{
    /**
     * @var \Redis|Client
     */
    private $client;

    /**
     * RedisLock constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'host' => '127.0.0.1',
            'password' => null,
            'port' => 6379,
            'database' => 0,
        ], $config);

        $host = $config['host'];
        $port = $config['port'];
        $password = $config['password'];
        $database = $config['database'];

        if ($this->_useRedis()) {
            $this->client = new \Redis();
            $this->client->pconnect($host, $port);
            if (!empty($password)) {
                $this->client->auth($password);
            }
            $this->client->select($database);
        } else {
            $this->client = new Client([
                'scheme' => 'tcp',
                'host'   => $host,
                'port'   => $port,
            ], [
                'parameters' => [
                    'password' => $password,
                    'database' => $database,
                ],
            ]);
        }
    }

    public function lock($key, $requestId, $expire)
    {
        $res = $this->_rawCommand('set', [$key, $requestId,'EX', $expire, 'NX']);
        return strtoupper($res) === 'OK';
    }

    public function unlock($key, $requestId)
    {
        $ret = $this->_rawCommand('get', [$key]);
        if ($ret == $requestId) {
            $this->_rawCommand('del', [$key]);
            return true;
        }
        return false;
    }

    private function _rawCommand($command, array $arguments)
    {
        if (get_class($this->client) == \Redis::class) {
            return $this->client->rawCommand($command, $arguments);
        } else {
            return $this->client->$command(...$arguments);
        }
    }

    /**
     * 是否使用 Redis 扩展
     * @return bool
     */
    private function _useRedis()
    {
        return class_exists(\Redis::class);
    }
}