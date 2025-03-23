<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPathsRequest;
use Paths\Services\PathService;

class PathController extends Controller
{
    public function __construct(
        private PathService $pathService
    ){}

    public function getPaths(GetPathsRequest $request){
        $data = $request->validated();

        return $this->pathService->findPaths($data['source'], $data['destination']);
    }
}
