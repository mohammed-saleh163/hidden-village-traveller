<?php

namespace App\Http\Controllers;

use Paths\Services\PathLockingService;
use App\Http\Requests\LockPathRequest;
use App\Http\Requests\ReserveRouteRequest;

class PathLocksController extends Controller
{
    public function __construct(
        private PathLockingService $pathLockingService,
    ){}


    public function reserveRoute(ReserveRouteRequest $request) {
        $data = $request->validated(); 
        
        return $this->pathLockingService->reserveRoute($data['route']);
    }

    public function lockRoute(LockPathRequest $request){
        $data = $request->validated();

        return $this->pathLockingService->lockRoute($data['route'], $data['time_to_lock']);
    }

    public function unlockRoute(ReserveRouteRequest $request){
        $route =  $request->validated()['route'];

        return $this->pathLockingService->unlockRoute($route);
    }
}
