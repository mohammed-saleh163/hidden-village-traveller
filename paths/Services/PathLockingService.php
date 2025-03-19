<?php

namespace Paths\Services;

use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PathLockingService {

      
  public function reserveRoute(string $route){
    $cacheKey = $this->getLockCacheKey($route, '-');

    if(Cache::has($cacheKey)){
        throw new HttpException(423, "Route is already in use");
    }

    
    $routeComplexity = $this->calculateRoadComplexity($route, 2);

    // time units are subject to change based on the preferred unit. 
    // the default remains in seconds now for testing simulation purposes 
    
    $locked = $this->lockRoute($route, $routeComplexity['road_complexity']);

    return[
      'locked' => $locked, 
      'route' => $route,
      'number_of_hops' => $routeComplexity['number_of_hops'],
      'route_complexity' => $routeComplexity['road_complexity'],
      'route_cache_key' => $cacheKey,
    ];
  }


  public function lockRoute(string $route, float $timeToLock, string $unit = 'seconds'){
      $cacheKey = $this->getLockCacheKey($route, '-');

      return Cache::put($cacheKey, true, now()->add($timeToLock, $unit));
  }

  public function unlockRoute(string $route){
      $cacheKey = $this->getLockCacheKey($route, '-');

      return Cache::forget($cacheKey);
  }

  protected function calculateRoadComplexity(string $route, int $hoursPerHop){
    
    $hops = substr_count($route, '->');
    
    $num = pow($hoursPerHop, $hops);

    $base = 2; 

    $complexity = log($num, $base);

      return [
        'number_of_hops' => $hops, 
        'road_complexity' => $complexity
      ]; 
  }

  protected function getLockCacheKey(string $route, $separator){
      $transformedString = str_replace(' -> ', $separator, $route); 

      $cacheKey = 'lock' . $separator . $transformedString;
      
      return strtolower($cacheKey);
  }

}