<?php

namespace Paths\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PathService
{
    public function __construct(){}

    public function findPaths(string $source, string $destination){
        $cacheKey = $source . '-' . $destination;

        $cacheExists = Cache::has($cacheKey);

        if($cacheExists){
            return Cache::get($cacheKey);
        }

        $cities = config('constants.CITIES');
        
        $startIndex = array_search($source, $cities);
        $destinationIndex = array_search($destination, $cities);
        
        $costs = config('constants.COSTS')[$startIndex];
        if($startIndex > $destinationIndex || $startIndex == $destinationIndex){
            throw new HttpException(400, 'Source and destination are invalid, cannout travel that route');
        }

        $paths = []; 
        $this->searchPaths($startIndex, $destinationIndex, $cities, $costs, 0, [], $paths);

        Cache::add($cacheKey, $paths, now()->addSecond(30));

        return $paths; 
    }

    private function searchPaths($startIndex, $destinationIndex, $cities, $costs, $totalCost, $initialPath, &$paths){

        $initialPath[] = $cities[$startIndex];

        if ($startIndex == $destinationIndex) { //base case
            $paths[] = [
                'route' => implode(" -> ", $initialPath),
                'cost' => $totalCost,
            ];
        }

        for ($nextIndex = $startIndex + 1; $nextIndex < count($cities); $nextIndex++) {
            if($costs[$nextIndex] > 0){
                $this->searchPaths($nextIndex, $destinationIndex, $cities, $costs, $totalCost + $costs[$nextIndex], $initialPath, $paths);
            }
        }
    }

}
