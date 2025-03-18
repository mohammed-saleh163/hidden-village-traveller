<?php

namespace Paths\Services;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PathLockingService {

      
  public function reserveRoute(string $route){
    $cacheKey = $this->getLockCacheKey($route);

    if(Cache::has($cacheKey)){
        throw new HttpException(423, "Route is already in use");
    }

    $hops = substr_count($route, '->');
    
    $routeComplexity = $this->calculateRoadComplexity($hops, 2);

    return $this->lockRoute($route, $routeComplexity);
}

private function lockRoute(string $route, int $routeComplexity){
    $cacheKey = $this->getLockCacheKey($route);

    return Cache::put($cacheKey, true, now()->addHours($routeComplexity));
}

protected function unlockRoute(string $route){
    $cacheKey = $this->getLockCacheKey($route);

    return Cache::forget($cacheKey);
}

private function calculateRoadComplexity(int $hops, int $hoursPerHop){
    $num = pow($hoursPerHop, $hops);
    $base = 2; 

    $complexity = log($num, $base);

    return $complexity; 
}

private function getLockCacheKey(string $route){
    $transformedString = str_replace(' -> ', '-', $route);
    $cacheKey = 'lock-' . $transformedString;

    return $cacheKey;
}

}