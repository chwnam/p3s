<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\P3S\Configuration;
use Chwnam\P3S\EditManager;
use Chwnam\P3S\Editors\DirectoriesExclusionEditor;
use Chwnam\P3S\Editors\RunDebugConfigurationXDebugEditor;
use Chwnam\P3S\Editors\VersionControlDirectoryMappingsEditor;
use Chwnam\P3S\Helpers\NodeHelper;
use Chwnam\P3S\Helpers\UrlPathHelper;

it('tests RunDebugConfigurationXDebugEditor', function () {
    // Build up test configuration
    $json                                      = getCommonJson();
    $json['setup']['runDebugConfiguration']['xdebug'] = true;

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);

    $workspace = $manager->getXml('workspace.xml');

    // Add fake server
    NodeHelper::addComponent($workspace, 'PhpServers')
              ->addChild('servers', true)
              ->addChild('server', '', [
                  'id'   => UrlPathHelper::getUuid4(),
                  'host' => 'wordpress.localhost',
                  'name' => 'wordpress.localhost',
                  'port' => '8443',
              ], true)
    ;

    $editor = new RunDebugConfigurationXDebugEditor($manager);
    $editor->edit();

    $configuration = $workspace->query('/project/component[@name="RunManager"]/configuration');
    expect($configuration->size())->toEqual(1);

    $node = $configuration->offsetGet(0);
    expect($node->getAttribute('type'))
        ->toBe('PhpRemoteDebugRunConfigurationType')
        ->and($node->getAttribute('factoryName'))->toBe('PHP Remote Debug')
        ->and($node->getAttribute('server_name'))->toBe('wordpress.localhost')
        ->and($node->getAttribute('session_id'))->toBe('phpstorm-xdebug')
    ;

    $method = $configuration->query('method');
    expect($method->size())
        ->toEqual(1)
        ->and($method->offsetGet(0)->getAttribute('v'))->toBe('2')
    ;
});
