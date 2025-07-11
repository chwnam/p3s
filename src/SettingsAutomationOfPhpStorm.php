<?php

namespace Chwnam\Saops;

use Chwnam\Saops\Editors\PhpComposerEditor;
use Chwnam\Saops\Editors\EditorNaturalLanguagesSpellingCustomDictionariesEditor;
use Chwnam\Saops\Editors\VersionControlDirectoryMappingsEditor;
use Chwnam\Saops\Editors\Editor;
use Chwnam\Saops\Editors\DirectoriesExclusionEditor;
use Chwnam\Saops\Editors\PhpLanguageLevelEditor;
use Chwnam\Saops\Editors\AppearanceAndBehaviorScopesEditor;
use Chwnam\Saops\Editors\PhpServersEditor;
use Chwnam\Saops\Editors\PhpFrameworksWordPressEditor;
use Chwnam\Saops\Editors\RunDebugConfigurationXDebugEditor;
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
