<?php

it('has install page', function () {
    $response = $this->get('/install');

    $response->assertStatus(200);
});
