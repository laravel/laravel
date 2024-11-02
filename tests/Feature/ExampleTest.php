<?php

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
