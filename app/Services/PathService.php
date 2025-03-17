<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PathService
{
    public function __construct(){}

    public function getPaths(string $source, string $destination){

        $cities = config('constants.CITIES');
        
        $sourceIndex = array_search($source, $cities);
        $destinationIndex = array_search($destination, $cities);
        
        
        $costsArray = config('constants.COSTS')[$sourceIndex]; 

        $paths = []; 
        for ($i = $sourceIndex + 1; $i < count($cities); $i++) {
           $route = $cities[$sourceIndex] . " -> " .join(" -> ", array_slice($cities, $i, $destinationIndex)); 
           $costSum = array_sum(array_slice($costsArray, $i, $destinationIndex));

           array_push($paths, [
               "route" => $route,
               "cost" => $costSum,]);
        }
        
        Cache::add($source . '-' . $destination, $paths, now()->addSecond(30));

        return $paths;
    }

}
