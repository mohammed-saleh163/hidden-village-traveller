<?php

test('Can fly from Tsuchi to Kaze', function () {
    $response = $this->post('/api/paths', [
        'source' => 'Tsuchi',
        'destination' => 'Kaze'
    ]);

    expect(count($response->json()))->toBe(2); // two routes lead to Kaze
    $response->assertStatus(200);
});

test('Can fly from Konoha to Kaze', function () {
    $response = $this->post('/api/paths', [
        'source' => 'Konoha',
        'destination' => 'Kaze'
    ]);
    
    expect($response[0]['route'])->toBe('Konoha -> Kaze');
    $response->assertStatus(200);
});


test('Cannot fly from south to north', function () {
    $response = $this->post('/api/paths', [
        'source' => 'Kaze',
        'destination' => 'Tsuchi'
    ]);

    $response->assertStatus(400);
});


test('Cannot fly from and to the same city', function () {
    $response = $this->post('/api/paths', [
        'source' => 'Tsuchi',
        'destination' => 'Tsuchi'
    ]);

    $response->assertStatus(400);
});

test('Costs are properly calculated', function () {
    $response = $this->post('/api/paths', [
        'source' => 'Tsuchi',
        'destination' => 'Mizu'
    ]);

    $allRoutesWithCosts = [
        "Tsuchi -> Konoha -> Kaze -> Mizu" => 160,
        "Tsuchi -> Konoha -> Mizu" => 95,
        "Tsuchi -> Kaze -> Mizu" => 150,
        "Tsuchi -> Mizu" => 85,
    ];

    for($i = 0; $i < count($response->json()); $i++){
        expect($response[$i]['cost'])->toBe($allRoutesWithCosts[$response[$i]['route']]);
    }

    $response->assertStatus(200);
});

test('All routes are being considered', function () {
    $response = $this->post('/api/paths', [
        'source' => 'Tsuchi',
        'destination' => 'Mizu'
    ]);

    $allRoutes = [
        "Tsuchi -> Konoha -> Kaze -> Mizu",
        "Tsuchi -> Konoha -> Mizu",
        "Tsuchi -> Kaze -> Mizu",
        "Tsuchi -> Mizu",
    ];
    for($i = 0; $i < count($response->json()); $i++){
        expect($response[$i]['route'])->toBeIn($allRoutes);
    }

    $response->assertStatus(200);
});

test('Traveling does not excede the destination', function () {
    $response = $this->post('/api/paths', [
        'source' => 'Tsuchi',
        'destination' => 'Konoha'
    ]);

    expect($response[0]['route'])->toBe("Tsuchi -> Konoha");

    $response->assertStatus(200);
});


test('The api returns the correct number of routes for every path', function () {
    $source = 'Konoha';
    $dest = 'Madara'; 

    $response = $this->post('/api/paths', [
        'source' => $source,
        'destination' => $dest,
    ]);

    $cities = config('constants.CITIES'); 
    $sourceIdx = array_search($source, $cities);
    $destIdx = array_search($dest, $cities);

    //Two of the elements are always excluded (source and destination are constants in every route)
    //however, since every item in between can be 'in' or 'out' of the set of locations we travel in each route, 
    //then it is more like a binary way of counting, hence the '2' in the pow()
    $diff = $destIdx - $sourceIdx;
    $expectedNum = pow(2, $diff - 1); // -1 so that source and dest are mutually exclusive

    expect(count($response->json()))->toBe($expectedNum);

    $response->assertStatus(200);  
});

test('All routes are distinct (no repitition)', function () {
    $source = 'Tsuchi'; 
    $dest = 'Madara'; 

    $response = $this->post('/api/paths', [
        'source' => $source,
        'destination' => $dest
    ]);

    //this will return an array of the routes only
    $routes = array_map(function($item){
        return $item['route'];
    }, $response->json());

    $uniqueRoutes = array_unique($routes, 0); //remove duplicates

    //compare counts. if equal, then there were no duplicate routes in the original array
    expect(count($routes))->toBe(count($uniqueRoutes));

    $response->assertStatus(200);
});

