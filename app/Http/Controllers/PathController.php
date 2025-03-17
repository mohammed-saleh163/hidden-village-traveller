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

        return $this->pathService->getPaths($data['source'], $data['destination']);
    }
}
