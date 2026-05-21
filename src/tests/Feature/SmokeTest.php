<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homepage returns 200', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
