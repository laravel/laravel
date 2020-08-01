<?php

it('has a home page', function () {
    $this->get('/')->assertOk();
});
