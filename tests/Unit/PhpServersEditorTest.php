<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\Saops\Configuration;
use Chwnam\Saops\EditManager;
use Chwnam\Saops\Editors\PhpServersEditor;

it('tests PhpServersEditor', function () {
    // Build up test configuration
    $json                            = getCommonJson();
    $json['setup']['php']['servers'] = [
        ['host' => 'my.localhost', 'port' => 8080],
        'preset:wordpress{' . getTestPath('/fixtures/fake-wp-cli') . '}',
    ];

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);
    $doc = $manager->getXml('workspace.xml');

    // Before edit
    $servers = $doc->query('/project/component[@name="PhpServers"]//server');
    expect($servers->size())->toEqual(0);

    $editor = new PhpServersEditor($manager);
    $editor->edit();

    // After edit
    $servers = $doc->query('/project/component[@name="PhpServers"]//server');
    expect($servers->size())
        ->toEqual(2)
        ->and($servers->offsetGet(0)->getAttribute('host'))->toEqual('my.localhost')
        ->and($servers->offsetGet(0)->getAttribute('port'))->toEqual('8080')
        ->and($servers->offsetGet(1)->getAttribute('host'))->toEqual('my-fake-wp.localhost')
        ->and($servers->offsetGet(1)->getAttribute('port'))->toEqual('8443')
    ;
});
