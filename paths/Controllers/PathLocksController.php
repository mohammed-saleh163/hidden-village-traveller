<?php

namespace Paths\Controllers;

use App\Http\Controllers\Controller;
use Paths\Services\PathLockingService;
use Paths\Requests\LockPathRequest;
use Paths\Requests\ReserveRouteRequest;

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
