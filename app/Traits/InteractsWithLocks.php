<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait InteractsWithLocks
{

    public function lock(string $key, $timeToLive = '1 minutes')
    {
        if (Cache::has($key)) {
            throw new HttpException(423, 'Already Locked');
        }

        $isLocked = Cache::put($key, true, now()->add($timeToLive));

        return [
            'locked' => $isLocked,
            'time_to_live' => $timeToLive,
            'cache_key' => $key,
        ];
    }

    public function unlock(string $cacheKey)
    {
        if (!Cache::has($cacheKey)) {
            throw new HttpException(404, 'There is no cache with that key');
        }

        $isUnlocked = Cache::forget($cacheKey);

        return $isUnlocked;
    }


    public function getLockKey(string $identifier)
    {
        $prefix = config('lock.prefix');
        
        $separator = config('lock.lock_key_separator');

        // $transformedString = preg_replace('/\s*(->|-|_|:| )\s*/', $separator, $identifier);

        $cacheKey = $prefix . $separator . $identifier;

        return strtolower($cacheKey);
    }
}
