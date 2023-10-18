<?php

namespace EK\Cache\Api;

use Illuminate\Support\Collection;

interface CacheInterface
{
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $data, int $ttl = 0): bool;
    public function delete(string $key): bool;
    public function getMultiple(array $keys, mixed $default = null): Collection;
    public function setMultiple(array $data, mixed $default = null): bool;
    public function deleteMultiple(array $keys): bool;
    public function clear(): bool;
    public function has(string $key): bool;
}
