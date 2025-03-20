<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetLockCacheKeyRequest;
use App\Http\Requests\LockPathRequest;
use App\Traits\InteractsWithLocks;

class PathLocksController extends Controller
{
    use InteractsWithLocks;


    public function lockRoute(LockPathRequest $request)
    {
        $cacheKey = $request->validated();

        return $this->lock($cacheKey);
    }

    public function unlockRoute(LockPathRequest $request)
    {
        $cacheKey =  $request->validated();

        return $this->unlock($cacheKey);
    }

    public function getLockCacheKey(GetLockCacheKeyRequest $request)
    {
        $identifier = $request->validated();

        return $this->getLockKey($identifier);
    }
}
