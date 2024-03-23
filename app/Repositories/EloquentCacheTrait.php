<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;

trait EloquentCacheTrait {

    /**
     * get cache key
     *
     * @param $key
     * @return string
     */
    public function getCacheKey($key)
    {
        return $key;
    }

    /**
     * get cache tag
     *
     * @param $tag
     * @return string
     */
    public function getCacheTag($tag = null)
    {
        if (empty($tag)) {
            $tag = get_called_class();
        }
        return $tag;
    }

    /**
     * get a cache item by key
     *
     * @param $key
     * @return mixed
     */
    public function getCacheByKey($key, $tag = null)
    {
        if (env('APP_ENV') == 'local') {
            return null;
        }
        return Cache::tags($this->getCacheTag($tag))->get($this->getCacheKey($key));

    }

    /**
     * set a cache item
     *
     * @param mixed $key
     * @param mixed $value
     * @param string $tag
     * @param bool $withCacheKeyGenertor
     */
    public function setCacheByKey($key, $value, $tag = null , $minutes = 100)
    {
        Cache::tags($this->getCacheTag($tag))->put($this->getCacheKey($key), $value, $minutes);
    }

    /**
     * remove a cache item
     *
     * @param string $key
     * @param string $key
     */
    public function removeCache($key, $tag = null)
    {
        Cache::tags($this->getCacheTag($tag))->forget($this->getCacheKey($key));
    }

    /**
     * remove a tagged caches
     *
     * @param $tag
     */
    public function removeThisClassCache($tag = null)
    {
        Cache::tags($this->getCacheTag($tag))->flush();
    }
}
