<?php

namespace Chwnam\P3S\Editors;

use Chwnam\P3S\EditManager;
use Chwnam\P3S\Helpers\NodeHelper;
use Chwnam\P3S\Helpers\UrlPathHelper;
use Chwnam\P3S\Templates\EditTemplate;
use DOMElement;
use Exception;
use FluidXml\FluidContext;
use FluidXml\FluidXml;

/**
 * @example
 * <component
 *      name="SpellCheckerSettings"
 *      RuntimeDictionaries="0"
 *      Folders="2"
 *      Folder0="$PROJECT_DIR$/../libs/custom-plugin-directory"
 *      Folder1="$PROJECT_DIR$/../libs/ko-aff-dic-0.7.94"
 *      CustomDictionaries="2"
 *      CustomDictionary0="$PROJECT_DIR$/../libs/custom-plugin-directory/custom.dic"
 *      CustomDictionary1="$PROJECT_DIR$/../libs/ko-aff-dic-0.7.94/ko.dic"
 *      DefaultDictionary="project-level"
 *      UseSingleDictionary="true"
 *      transferred="false"
 * />
 */
readonly class EditorNaturalLanguagesSpellingCustomDictionariesEditor implements Editor
{
    public function __construct(private EditManager $manager)
    {
    }

    /**
     * @throws Exception
     */
    public function edit(): void
    {
        EditTemplate
            ::create($this->manager->getXml($this->getDefaultFileName()))
            ->findNode(NodeHelper::getComponentQuery($this->getComponentName()))
            ->doWhenNodeNotFound(function (FluidXml $xml): FluidContext {
                return NodeHelper::addComponent($xml, $this->getComponentName());
            })
            ->doForTarget(function (DomElement $node, FluidContext $nodes) {
                $configSetup = $this->getConfigSetup();
                if ($configSetup && false === $configSetup[0]) {
                    return;
                }

                // Remove all attributes of the component
                $folders = (int)$node->getAttribute('Folders');
                for ($i = 0; $i < $folders; $i++) {
                    $node->removeAttribute("Folder$i");
                }
                $directories = (int)$node->getAttribute('CustomDictionaries');
                for ($i = 0; $i < $directories; $i++) {
                    $node->removeAttribute("CustomDictionary$i");
                }

                // Build items
                $items = array_unique(array_filter(array_merge($this->getIdeaSetup(), $this->getConfigSetup())));
                sort($items);
                $folders = array_unique(array_map(fn($item) => dirname($item), $items));

                // Add new attributes.
                $node->setAttribute('RuntimeDictionaries', '0');
                $node->setAttribute('Folders', count($folders));
                foreach ($folders as $i => $folder) {
                    $node->setAttribute("Folder$i", $folder);
                }
                $node->setAttribute('CustomDictionaries', count($items));
                foreach ($items as $i => $item) {
                    $node->setAttribute("CustomDictionary$i", $item);
                }
                $node->setAttribute('DefaultDictionary', 'project-level');
                $node->setAttribute('UseSingleDictionary', 'true');
                $node->setAttribute('transferred', 'false');
            })
            ->done()
        ;
    }

    public function getDefaultFileName(): string
    {
        return 'workspace.xml';
    }

    /**
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $xml   = $this->manager->getXml($this->getDefaultFileName());
        $nodes = $xml->query(NodeHelper::getComponentQuery($this->getComponentName()));

        if (0 === $nodes->size()) {
            return [];
        }

        $output = [];
        $count  = (int)$nodes[0]->getAttribute('CustomDictionaries');

        for ($i = 0; $i < $count; $i++) {
            $output[] = $nodes[0]->getAttribute("CustomDictionary$i");
        }

        return $output;
    }

    public function getConfigSetup(): array
    {
        $values = $this->manager->getConfig()->getSetup($this->getDefaultConfigParam(), false);
        if (false === $values) {
            return [false];
        }

        return array_unique(
            array_filter(
                array_map(
                    function (string $item): string {
                        if (!str_starts_with($item, '/')) {
                            $item = str_replace(
                                search: '/./',
                                replace: '/',
                                subject: $this->manager->getConfig()->getTarget() . '/' . $item,
                            );
                        }
                        return UrlPathHelper::asProjectPath($item, $this->manager->getConfig()->getProjectRoot());
                    },
                    (array)$this->manager->getConfig()->getSetup($this->getDefaultConfigParam()),
                ),
            ),
        );
    }

    public function getComponentName(): string
    {
        return 'SpellCheckerSettings';
    }

    public function getDefaultConfigParam(): string
    {
        return 'editor.naturalLanguages.spelling.customDictionaries';
    }
}
