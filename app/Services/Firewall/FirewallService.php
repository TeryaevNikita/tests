<?php

namespace App\Services\Firewall;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class FirewallService
{
    const CHUNK_SIZE = 2;
    const TABLE_NAME = 'banned_ip';
    const KEY_PREFIX = 'banned.ip.';
    const LOADED_TO_CACHE_KEY = 'loaded_baned_ip';
    const CACHE_STATUS_EMPTY = 'empty';
    const CACHE_STATUS_LOADING = 'loading';
    const CACHE_STATUS_READY = 'ready';
    const CACHE_READY_TTL_SEC = 3600;

    /**
     * @param string $ip
     * @return bool
     */
    public function check(string $ip): bool
    {
        $cacheStatus = $this->getCacheStatus();

        // need load data to cache
        if ($cacheStatus == self::CACHE_STATUS_EMPTY) {
            $this->setCacheStatus(self::CACHE_STATUS_LOADING);
            $this->loadToCache();
            $this->setCacheStatus(self::CACHE_STATUS_READY, self::CACHE_READY_TTL_SEC);
        }

        // service not ready for work
        if ($cacheStatus == self::CACHE_STATUS_LOADING) {
            return true;
        }

        $key = $this->getCacheKey($ip);

        $res = Redis::exists($key);

        return $res;
    }

    /**
     * @param array $ips
     */
    public function set(array $ips)
    {
        $this->clearDb();
        $chunks = array_chunk($ips, self::CHUNK_SIZE);

        foreach ($chunks as $chunk) {
            $res = array_map(
                function ($value) {
                    return ['ip' => $value];
                },
                $chunk
            );

            DB::table($this->getTableName())->insertOrIgnore($res);
        }
    }

    private function clearDb()
    {
        DB::table($this->getTableName())->truncate();
    }

    private function loadToCache()
    {
        $this->clearCache();

        DB::table(self::TABLE_NAME)->select('ip')->orderBy('id')->chunk(100, function ($ips) {
            $this->addToCache($ips->pluck('ip')->toArray());
        });
    }

    private function clearCache()
    {
        $options = Redis::getOptions();

        $laraPrefix = $options->prefix->getPrefix();
        $keys = Redis::keys(self::KEY_PREFIX . "*");

        foreach ($keys as $key) {
            Redis::del(str_replace($laraPrefix, '', $key));
        }

    }

    private function addToCache(array $ips)
    {
        foreach ($ips as $ip) {
            $key = $this->getCacheKey($ip);
            Redis::set($key, $ip);
        }
    }

    private function getCacheStatus(): string
    {
        return Redis::get(self::LOADED_TO_CACHE_KEY) ?? self::CACHE_STATUS_EMPTY;
    }

    private function setCacheStatus(string $status, int $expire = null)
    {
        $key = self::LOADED_TO_CACHE_KEY;
        Redis::set($key, $status);

        if ($expire != null) {
            Redis::expire($key, $expire);
        }
    }

    private function getCacheKey(string $ip): string
    {
        return self::KEY_PREFIX . $ip;
    }

    private function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}
