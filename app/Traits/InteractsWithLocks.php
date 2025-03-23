<?php

namespace App\Traits;

use App\Exceptions\LockedActionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait InteractsWithLocks
{

    public function isLocked(string $identifier) {
        $key = $this->getLockKey($identifier);

        return Cache::has($key);
    }

    public function lock(string $identifier, int $timeToLive = 1): bool
    {

        $key = $this->getLockKey($identifier);

        if (Cache::has($key)) {
            throw new LockedActionException();
        }

        $lock = Cache::lock($key, $timeToLive); // lock will be automatically released after the $timeToLive expires

        if(!$lock->get()) {
            throw new LockedActionException();
        }

        $isLocked = Cache::put($key, $lock->owner(), now()->addSeconds($timeToLive));

        return $isLocked;
    }

    public function unlock(string $identifier): bool
    {
        $key = $this->getLockKey($identifier);

        if (!Cache::has($key)) {
            throw new HttpException(404, 'There is no lock with that key');
        }

        $lockOwner = Cache::get($key);

        $lock = Cache::restoreLock($key, $lockOwner);
        
        $lockReleased = $lock->release();

        $isUnlocked = Cache::forget($key);

        return $lockReleased && $isUnlocked;
    }


    public function getLockKey(string $identifier): string
    {
        $prefix = config('lock.prefix');
        
        $separator = config('lock.lock_key_separator');

        $key = $prefix . $separator . $identifier;

        $hashedKey = hash('sha256', $key);

        return $hashedKey;
    }
}
