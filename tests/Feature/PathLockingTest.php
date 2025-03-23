<?php

use App\Helpers\Complexity;
use Paths\Services\PathService;

function getRandomRoute(string $source, string $destination) {
    $routes = new PathService()->findPaths($source, $destination);

    $routeIndex = random_int(0, count($routes) - 1); // pick random route
    $route = $routes[$routeIndex]['route']; 

    return $route;
}

function getPathKey(string $route) {
    $identifier = preg_replace('/\s*(->|-|_|:| )\s*/', config('lock.lock_key_separator'), $route);
    return $identifier;
}

function getTtl($route){
    $hops = substr_count($route, '->');
    $ttl = (int) Complexity::calculateComplexity($hops, 2);

    return $ttl;

}


test('Paths can be reserved', function () {
    $route = getRandomRoute('Tsuchi', 'Mizu');

    $cacheKey = getPathKey($route);
   
    $ttl = getTtl($route);

    $reservationResponse = $this->post('/api/paths/lock-path', [
        'cache_key' => $cacheKey,
        'time_to_live' => $ttl,
    ]);

    expect((bool)$reservationResponse->json())->toBeTrue();
    $reservationResponse->assertStatus(200);
});


test('Locked paths cannot be reserved', function () {
    $route = getRandomRoute('Tsuchi', 'Mizu');

    $cacheKey = getPathKey($route);
    $ttl = getTtl($route);
    
    $this->post('/api/paths/lock-path', [
        'cache_key' => $cacheKey,
        'time_to_live' => $ttl,
    ]);


    $reservationResponse2 = $this->post('/api/paths/lock-path', [
        'cache_key' => $cacheKey,
        'time_to_live' => $ttl,
    ]);

    $reservationResponse2->assertStatus(423); // Locked HTTP status
});



test('Routes can be reserved again after the complexity time passes', function () {
    $route = getRandomRoute('Tsuchi', 'Kaze');

    $ttl = getTtl($route);
    $cacheKey = getPathKey($route);

    $reservationResponse = $this->post('/api/paths/lock-path', [
        'cache_key' => $cacheKey,
        'time_to_live' => $ttl,
    ]);


    sleep($ttl); // Pause thread execution

    $reservationResponse2 = $this->post('/api/paths/lock-path', [
        'cache_key' => $cacheKey,
        'time_to_live' => $ttl,
    ]);

    expect((bool)$reservationResponse->json())->toBe(true);
    expect((bool)$reservationResponse2->json())->toBe(true);

    $reservationResponse->assertStatus(200);
    $reservationResponse2->assertStatus(200);
});

test('Locked routes can be unlocked again', function (){

    $route = getRandomRoute('Konoha', 'Hokage');

    $cacheKey = getPathKey($route);

    $lockResponse = $this->post('/api/paths/lock-path', [
        'cache_key' => $cacheKey,
        'time_to_live' => 1000000000,
    ]);
    
    $lockResponse->assertStatus(200);

    
    $unlockResponse = $this->post('/api/paths/unlock-path', [
        'cache_key' => $cacheKey,
    ]);

    // dd($unlockResponse->json());
    expect( (bool) $unlockResponse->json())->toBeTrue();

    $unlockResponse->assertStatus(200);
});