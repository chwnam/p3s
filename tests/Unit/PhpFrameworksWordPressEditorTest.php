<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\Saops\Configuration;
use Chwnam\Saops\EditManager;
use Chwnam\Saops\Editors\PhpFrameworksWordPressEditor;

it('tests PhpFrameworksWordPressEditor', function () {
    // Build up test configuration
    $json                                            = getCommonJson();
    $json['setup']['php']['frameworks']['wordpress'] = [
        'enabled'          => true,
        'installationPath' => $json['projectRoot'],
    ];

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);
    $doc = $manager->getXml('workspace.xml');

    // Before edit
    $path = $doc->query('/project/component[@name="WordPressConfiguration"]//wordpressPath');
    expect($path->size())->toEqual(0);

    $editor = new PhpFrameworksWordPressEditor($manager);
    $editor->edit();

    // After edit
    $component = $doc->query('/project/component[@name="WordPressConfiguration"]');
    expect($component->offsetGet(0)->getAttribute('enabled'))->toEqual('true');

    $path = $doc->query('/project/component[@name="WordPressConfiguration"]/wordpressPath');
    expect($path->size())
        ->toEqual(1)
        ->and($path->offsetGet(0)->textContent)->toEqual($json['projectRoot'])
    ;
});