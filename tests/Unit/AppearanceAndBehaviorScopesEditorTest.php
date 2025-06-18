<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\P3S\Configuration;
use Chwnam\P3S\EditManager;
use Chwnam\P3S\Editors\AppearanceAndBehaviorScopesEditor;

it('tests AppearanceAndBehaviorScopesEditor', function () {
    // Build up test configuration
    $json                                             = getCommonJson();
    $json['setup']['appearanceAndBehavior']['scopes'] = true;

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);
    $doc = $manager->getXml('workspace.xml');

    // Before edit
    $path = $doc->query('/project/component[@name="NamedScopeManager"]');
    expect($path->size())->toEqual(0);

    $editor = new AppearanceAndBehaviorScopesEditor($manager);
    $editor->edit();

    // After edit
    $scope   = $doc->query('/project/component[@name="NamedScopeManager"]/scope');
    $pattern = $scope->offsetGet(0)->getAttribute('pattern');
    expect($scope->size())
        ->toEqual(1)
        ->and($pattern)->not()->toBeEmpty()
    ;
});
