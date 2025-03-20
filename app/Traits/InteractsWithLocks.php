<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait InteractsWithLocks
{

    public function lock(string $key, int $timeToLive = 1): bool
    {
        if (Cache::has($key)) {
            throw new HttpException(423, 'Already Locked');
        }

        $isLocked = Cache::put($key, true, now()->addSeconds($timeToLive));

        return $isLocked;
    }

    public function unlock(string $cacheKey): bool
    {
        if (!Cache::has($cacheKey)) {
            throw new HttpException(404, 'There is no cache with that key');
        }

        $isUnlocked = Cache::forget($cacheKey);

        return $isUnlocked;
    }


    public function getLockKey(string $identifier): string
    {
        $prefix = config('lock.prefix');
        
        $separator = config('lock.lock_key_separator');

        // $transformedString = preg_replace('/\s*(->|-|_|:| )\s*/', $separator, $identifier);

        $cacheKey = $prefix . $separator . $identifier;

        return strtolower($cacheKey);
    }
}
