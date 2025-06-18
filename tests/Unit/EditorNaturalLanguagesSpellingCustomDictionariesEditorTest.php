<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\P3S\Configuration;
use Chwnam\P3S\EditManager;
use Chwnam\P3S\Editors\EditorNaturalLanguagesSpellingCustomDictionariesEditor;

it('tests EditorNaturalLanguagesSpellingCustomDictionariesEditor', function () {
    // Build up test configuration
    $json                                                                          = getCommonJson();
    $json['setup']['editor']['naturalLanguages']['spelling']['customDictionaries'] = [
        './custom-1.dic',
        './docs/custom-2.dic',
    ];

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);
    $doc = $manager->getXml('workspace.xml');

    // Before edit
    $path = $doc->query('/project/component[@name="SpellCheckerSettings"]');
    expect($path->size())->toEqual(0);

    $editor = new EditorNaturalLanguagesSpellingCustomDictionariesEditor($manager);
    $editor->edit();

    // After edit
    $component = $doc->query('/project/component[@name="SpellCheckerSettings"]');
    expect($component->size())
        ->toEqual(1)
        ->and($component[0]->getAttribute('Folders'))->toBe('2')
        ->and($component[0]->getAttribute('CustomDictionaries'))->toBe('2')
        ->and($component[0]->getAttribute('CustomDictionary0'))
        /* CustomDictionary0 */ ->toBe('$PROJECT_DIR$/wp-content/plugins/fake-plugin-1/custom-1.dic')
        ->and($component[0]->getAttribute('CustomDictionary1'))
        /* CustomDictionary1 */ ->toBe('$PROJECT_DIR$/wp-content/plugins/fake-plugin-1/docs/custom-2.dic')
    ;
});
