<?php

namespace Paths\Controllers;

use Paths\Requests\GetPathsRequest;
use Paths\Services\PathService;
use App\Http\Controllers\Controller;

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
