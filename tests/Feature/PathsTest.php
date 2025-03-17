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
