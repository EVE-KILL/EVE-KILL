<?php

namespace EK\Cache;

use Predis\Client;
use Exception;
use Illuminate\Support\Collection;
use EK\Cache\Api\CacheInterface;
use EK\Cache\Connection;

class Cache implements CacheInterface
{
    protected Client $redis;

    public function __construct(Connection $connection)
    {
        $this->redis = $connection->getClient();
    }

    final public function get(string $key, mixed $default = null): mixed
    {
        try {
            return $this->has($key) ?
                json_decode($this->redis->get($key), true, 512, JSON_THROW_ON_ERROR) :
                $default;
        } catch (Exception $e) {
            return null;
        }
    }

    final public function set(string $key, mixed $data, int $ttl = 0): bool
    {
        try {
            if ($ttl > 0) {
                return $this->redis->set($key, json_encode($data, JSON_THROW_ON_ERROR), $ttl);
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    final public function delete(string $key): bool
    {
        return $this->redis->del($key);
    }

    final public function getMultiple(array $keys, mixed $default = null): Collection
    {
        $return = $this->redis->mget($keys);

        if (empty($return)) {
            return $default instanceof Collection ? $default : collect($default);
        }
        return $return;
    }

    final public function setMultiple(array $data, mixed $default = null, int $ttl = 0): bool
    {
        if ($ttl > 0) {
            $multi = $this->redis->multi();
            foreach ($data as $key => $value) {
                $multi->set($key, $value, $ttl);
            }
            $multi->exec();

            return true;
        }

        return false;
    }

    /**
     * @return true
     */
    final public function deleteMultiple(array $keys): bool
    {
        $multi = $this->redis->multi();
        foreach ($keys as $key) {
            $multi->del($key);
        }
        $multi->exec();

        return true;
    }

    final public function clear(): bool
    {
        return $this->redis->flushDB();
    }

    final public function has(string $key): bool
    {
        return $this->redis->exists($key);
    }
}
