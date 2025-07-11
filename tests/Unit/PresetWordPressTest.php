<?php

use Chwnam\Saops\Presets\WordPress;

it('tests WordPress::getServer', function () {
    $fake_wp = getTestPath('/fixtures/fake-wp-cli');
    $result  = WordPress::getServerInfo("preset:wordpress{{$fake_wp}}");

    expect($result)->toBe('my-fake-wp.localhost:8443');
});
