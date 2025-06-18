<?php

namespace Chwnam\P3S;

use Chwnam\P3S\Editors\PhpComposerEditor;
use Chwnam\P3S\Editors\EditorNaturalLanguagesSpellingCustomDictionariesEditor;
use Chwnam\P3S\Editors\VersionControlDirectoryMappingsEditor;
use Chwnam\P3S\Editors\Editor;
use Chwnam\P3S\Editors\DirectoriesExclusionEditor;
use Chwnam\P3S\Editors\PhpLanguageLevelEditor;
use Chwnam\P3S\Editors\AppearanceAndBehaviorScopesEditor;
use Chwnam\P3S\Editors\PhpServersEditor;
use Chwnam\P3S\Editors\PhpFrameworksWordPressEditor;
use Chwnam\P3S\Editors\RunDebugConfigurationXDebugEditor;
use Exception;

class SettingsAutomationOfPhpStorm
{
    private EditManager $manager;

    /**
     * @throws Exception
     */
    public function __construct(string $configPath)
    {
        $this->manager = new EditManager($configPath);
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $exceptionTriggered = false;

        foreach ($this->getEditorClasses() as $editorClass) {
            if (!class_exists($editorClass) || !is_subclass_of($editorClass, Editor::class)) {
                throw new Exception("Editor class $editorClass does not exist or is not a subclass of Editor.");
            }
            try {
                $editor = new $editorClass($this->manager);
                $editor->edit();
            } catch (Exception $e) {
                $exceptionTriggered = true;
                if (isset($editor)) {
                    $classes = explode('\\', get_class($editor));
                    printf('Edit of %s has failed: %s', $classes[count($classes) - 1], $e->getMessage());
                }
            }
        }

        if ($exceptionTriggered) {
            printf("\nDue to one or more exceptions have occurred, we do not save the result.\n");
        } else {
            $this->manager->finish();
        }
    }

    private function getEditorClasses(): array
    {
        return [
            PhpLanguageLevelEditor::class,
            PhpServersEditor::class,
            PhpComposerEditor::class,
            PhpFrameworksWordPressEditor::class,
            AppearanceAndBehaviorScopesEditor::class,
            EditorNaturalLanguagesSpellingCustomDictionariesEditor::class,
            DirectoriesExclusionEditor::class,
            VersionControlDirectoryMappingsEditor::class,
            RunDebugConfigurationXDebugEditor::class,
        ];
    }
}
