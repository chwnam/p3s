<?php
/** @noinspection PhpUnhandledExceptionInspection */

use FluidXml\FluidXml;

it('tests servers/workspace.xml server list parsing', function () {
    $xml = FluidXml::load(dirname(__DIR__) . '/fixtures/servers/workspace.xml');

    $phpServers = $xml->query('/project[@version="4"]/component[@name="PhpServers"]');
    $servers    = $phpServers->query('//server');
    $names      = [];
    $ports      = [];

    foreach ($servers as $server) {
        $names[] = $server->getAttribute('name');
        $ports[] = $server->getAttribute('port');
    }

    expect($names)
        ->toEqual(['dummy', 'dummy2'])
        ->and($ports)->toEqual(['', '8443'])
    ;
});
