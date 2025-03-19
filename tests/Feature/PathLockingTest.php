<?php

test('Paths can be reserved', function () {
    $source = 'Tsuchi';
    $dest = 'Konoha';

    $routesResponse = $this->post('/api/paths', [
        'source' => $source,
        'destination' => $dest,
    ]);

    $routeIndex = random_int(0, count($routesResponse->json()) - 1); // pick random route
    $route = $routesResponse->json()[$routeIndex]['route']; 

    $reservationResponse = $this->post('/api/paths/reserve-path', [
        'route' => $route,
    ]);

    expect($reservationResponse->json()['locked'])->toBe(true);
    $reservationResponse->assertStatus(200);
});


test('Locked paths cannot be reserved', function () {
    $source = 'Tsuchi';
    $dest = 'Mizu';

    $routesResponse = $this->post('/api/paths', [
        'source' => $source,
        'destination' => $dest,
    ]);

    $routeIndex = random_int(0, count($routesResponse->json()) - 1);
    $route = $routesResponse->json()[$routeIndex]['route']; 

    $this->post('/api/paths/reserve-path', [
        'route' => $route,
    ]);

    $reservationResponse2 = $this->post('/api/paths/reserve-path', [
        'route' => $route,
    ]);

    $reservationResponse2->assertStatus(423); // Locked HTTP status
});

test('The API returns the corrects number of hops for the route', function () {
    $source = 'Tsuchi';
    $dest = 'Hokage';

    $routesResponse = $this->post('/api/paths', [
        'source' => $source,
        'destination' => $dest,
    ]);

    $routeIndex = random_int(0, count($routesResponse->json()) - 1);
    $route = $routesResponse->json()[$routeIndex]['route']; 
    $hopsCount = substr_count($route, ' -> ');
    $reservationResponse = $this->post('/api/paths/reserve-path', [
        'route' => $route,
    ]);

    expect($reservationResponse->json()['number_of_hops'])->toBe($hopsCount);
    $reservationResponse->assertStatus(200);
});



test('Routes can be reserved again after the complexity time passes', function () {
    $source = 'Tsuchi';
    $dest = 'Konoha';

    $routesResponse = $this->post('/api/paths', [
        'source' => $source,
        'destination' => $dest,
    ]);

    $routeIndex = random_int(0, count($routesResponse->json()) - 1);
    $route = $routesResponse->json()[$routeIndex]['route']; 


    $reservationResponse = $this->post('/api/paths/reserve-path', [
        'route' => $route,
    ]);

    $complexity = $reservationResponse->json()['route_complexity'];

    sleep($complexity); // Pause thread execution

    $reservationResponse2 = $this->post('/api/paths/reserve-path', [
        'route' => $route,
    ]);

    expect($reservationResponse2->json()['locked'])->toBe(true);
    

    $reservationResponse->assertStatus(200);
    $reservationResponse2->assertStatus(200);
});

test('Locked routes can be unlocked again', function (){

    $source = 'Tsuchi';
    $dest = 'Konoha';

    $routesResponse = $this->post('/api/paths', [
        'source' => $source,
        'destination' => $dest,
    ]);

    $routeIndex = random_int(0, count($routesResponse->json()) - 1);
    $route = $routesResponse->json()[$routeIndex]['route']; 


    $lockResponse = $this->post('/api/paths/lock-path', [
        'route' => $route,
        'time_to_lock' => 1000000000,
    ]);

    
    $unlockResponse = $this->post('/api/paths/unlock-path', [
        'route' => $route,
    ]);

    expect( (bool) $unlockResponse->json())->toBeTrue();

    $unlockResponse->assertStatus(200);
});