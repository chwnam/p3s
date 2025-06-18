<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\P3S\Configuration;
use Chwnam\P3S\EditManager;
use Chwnam\P3S\Editors\PhpComposerEditor;
use Chwnam\P3S\Helpers\UrlPathHelper;

it('tests ComposerEditor', function () {
    // Build up test configuration
    $json                             = getCommonJson();
    $json['setup']['php']['composer'] = true;

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);
    $doc = $manager->getXml('workspace.xml');

    // Before edit
    $path = $doc
        ->query('/project/component[@name="ComposerSettings"]/pharConfigPath')
        ->offsetGet(0)
        ->textContent;
    expect($path)->toBeEmpty();

    $editor = new PhpComposerEditor($manager);
    $editor->edit();

    // After edit
    $path = $doc
        ->query('/project/component[@name="ComposerSettings"]/pharConfigPath')
        ->offsetGet(0)
        ->textContent;

    expect($path)->toEqual(
        UrlPathHelper::asProjectPath(
            path: $config->getTarget() . '/composer.json',
            projectRoot: $config->getProjectRoot(),
        ),
    );
});
