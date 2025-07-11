<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\Saops\Configuration;
use Chwnam\Saops\EditManager;
use Chwnam\Saops\Editors\DirectoriesExclusionEditor;
use Chwnam\Saops\Editors\VersionControlDirectoryMappingsEditor;

it('tests VersionControlDirectoryMappingsEditor', function () {
    // Build up test configuration
    $json                                                 = getCommonJson();
    $json['setup']['versionControl']['directoryMappings'] = 'preset:wordpress';

    $projectRoot = $json['projectRoot'];
    $target      = $json['target'];

    // Make .git directory
    $gitDirs = [
        $projectRoot . '/wp-content/plugins/fake-plugin-1',
        $projectRoot . '/wp-content/plugins/fake-plugin-2',
        $projectRoot . '/wp-content/themes/fake-theme-1',
        // Not fake-theme-2
    ];
    foreach ($gitDirs as $gitDir) {
        if (!file_exists($gitDir . '/.git')) {
            mkdir($target . '/.git');
        }
    }

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);

    $vcs       = $manager->getXml('vcs.xml');
    $workspace = $manager->getXml('workspace.xml');

    $editor = new VersionControlDirectoryMappingsEditor($manager);
    $editor->edit();

    // After edit
    $mapping = $vcs->query('/project/component/mapping');
    expect($mapping->size())
        ->toEqual(1)
        ->and($mapping->offsetGet(0)->getAttribute('directory'))
        ->toBe('$PROJECT_DIR$/wp-content/plugins/fake-plugin-1')
    ;

    $ignoredRoots = $workspace->query('/project/component/ignored-roots');
    expect($ignoredRoots->size())->toEqual(1);

    $paths = $workspace->query('/project/component/ignored-roots/path');
    expect($paths->size())->toBe(2);

    $values = [];
    foreach ($paths as $path) {
        $values[] = $path->getAttribute('value');
    }
    sort($values);

    expect($values)->toBe([
        '$PROJECT_DIR$/wp-content/plugins/fake-plugin-2',
        '$PROJECT_DIR$/wp-content/themes/fake-theme-1',
    ]);
});
