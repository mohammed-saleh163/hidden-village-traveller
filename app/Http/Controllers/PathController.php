<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPathsRequest;
use App\Services\PathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PathController extends Controller
{
    public function __construct(
        private PathService $pathService
    ){}

    public function getPaths(GetPathsRequest $request){
        $data = $request->validated();
        $cacheKey = $data['source'] . '-' . $data['destination'];
        
        $cacheExists = Cache::has($cacheKey);

        if($cacheExists){
            return Cache::get($cacheKey);
        }
        else {
            return $this->pathService->getPaths($data['source'], $data['destination']);
        }

    }
}
