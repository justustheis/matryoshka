<?php

namespace JustusTheis\Matryoshka;

use Illuminate\Contracts\Cache\Repository as Cache;

class RussianCaching
{
    /**
     * The cache repository.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Create a new class instance.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Put to the cache.
     *
     * @param  mixed   $key
     * @param  string  $fragment
     * @param  string  $tags
     *
     * @return mixed
     */
    public function put($key, $fragment, $tags = 'views')
    {
        $key = $this->normalizeCacheKey($key);

        return $this->cache
            ->tags($tags)
            ->rememberForever($key, function () use ($fragment) {
                return $fragment;
            });
    }

    /**
     * Check if the given key exists in the cache.
     *
     * @param  mixed   $key
     * @param  string  $tags
     *
     * @return mixed
     */
    public function has($key, $tags = 'views')
    {
        $key = $this->normalizeCacheKey($key);

        return $this->cache
            ->tags($tags)
            ->has($key);
    }

    /**
     * Normalize the cache key.
     *
     * @param  mixed  $key
     *
     * @return mixed
     */
    protected function normalizeCacheKey($key)
    {
        if (is_object($key) && method_exists($key, 'getCacheKey')) {
            return $key->getCacheKey();
        }

        return $key;
    }
}

