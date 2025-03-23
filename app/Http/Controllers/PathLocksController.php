<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetLockCacheKeyRequest;
use App\Http\Requests\LockPathRequest;
use App\Http\Requests\UnlockPathRequest;
use App\Traits\InteractsWithLocks;

class PathLocksController extends Controller
{
    use InteractsWithLocks;


    public function lockRoute(LockPathRequest $request): bool
    {
        $data = $request->validated();

        return $this->lock($data['cache_key'], $data['time_to_live']);
    }

    public function unlockRoute(UnlockPathRequest $request)
    {
        $cacheKey =  $request->validated()['cache_key'];

        return $this->unlock($cacheKey);
    }

    public function getLockCacheKey(GetLockCacheKeyRequest $request)
    {
        $identifier = $request->validated()['identifier'];

        return $this->getLockKey($identifier);
    }
}
