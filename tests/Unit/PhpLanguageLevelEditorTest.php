<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Chwnam\P3S\Configuration;
use Chwnam\P3S\EditManager;
use Chwnam\P3S\Editors\PhpLanguageLevelEditor;

it('tests PhpLanguageLevelEditor', function () {
    // Build up test configuration
    $json                                  = getCommonJson();
    $json['setup']['php']['languageLevel'] = '8.2';

    $config  = new Configuration($json);
    $manager = new EditManager();
    $manager->setConfig($config);

    $editor = new PhpLanguageLevelEditor($manager);
    $editor->edit();

    // After edit
    $xml     = $manager->getXml('php.xml');
    $version = $xml
        ->query('/project/component[@name="PhpProjectSharedConfiguration"]')
        ->offsetGet(0)
        ->getAttribute('php_language_level')
    ;
    $this->assertEquals('8.2', $version);
});
