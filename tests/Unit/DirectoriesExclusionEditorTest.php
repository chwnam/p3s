<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\P3S\Configuration;
use Chwnam\P3S\EditManager;
use Chwnam\P3S\Editors\DirectoriesExclusionEditor;

it('tests DirectoriesExclusionEditor', function () {
    // Build up test configuration
    $json                                      = getCommonJson();
    $json['setup']['directories']['exclusion'] = 'preset:wordpress';

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);
    $xml = $manager->getXml('fake.wordpress.iml');

    $editor = new DirectoriesExclusionEditor($manager);
    $editor->edit();

    $component = $xml->query('/module[@type="WEB_MODULE"][@version="4"]/component[@name="NewModuleRootManager"]');;
    expect($component->size())->toEqual(1);

    $content = $component->query('content');
    expect($content->size())
        ->toEqual(1)
        ->and($content->offsetGet(0)->getAttribute('url'))
        ->toBe('file://$MODULE_DIR$')
    ;

    $sourceFolder = $content->query('sourceFolder');
    expect($sourceFolder->size())
        ->toEqual(1)
        ->and($sourceFolder->offsetGet(0)->getAttribute('url'))
        ->toBe('file://$MODULE_DIR$/wp-content/plugins/fake-plugin-1')
    ;

    $excludeFolders = $content->query('excludeFolder');
    expect($excludeFolders->size())->toEqual(5);

    $excluded = [];
    foreach ($excludeFolders as $item) {
        $excluded[] = $item->getAttribute('url');
    }
    sort($excluded);

    expect($excluded)->toEqual([
        'file://$MODULE_DIR$/wp-content/plugins/fake-plugin-2',
        'file://$MODULE_DIR$/wp-content/themes/fake-theme-1',
        'file://$MODULE_DIR$/wp-content/themes/fake-theme-2',
        'file://$MODULE_DIR$/wp-content/upgrade',
        'file://$MODULE_DIR$/wp-content/uploads',
    ]);
});
