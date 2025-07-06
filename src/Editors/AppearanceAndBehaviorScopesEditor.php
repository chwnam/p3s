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
 * Editor for setup.php.servers
 */
readonly class AppearanceAndBehaviorScopesEditor implements Editor
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
            ->doForTarget(function (DOMElement $node, FluidContext $nodes) {
                [$enabled] = $this->getConfigSetup();
                $ideaSetup = $this->getIdeaSetup();

                if (!$enabled) {
                    return;
                }

                $name = basename($this->manager->getConfig()->getTarget());
                // Skip if $name found.
                foreach ($ideaSetup as $item) {
                    if ($item['name'] === $name) {
                        return;
                    }
                }

                $pattern = $this->getScopesPattern(
                    $this->manager->getConfig()->getProjectRoot(),
                    $this->manager->getConfig()->getTarget(),
                );
                if ($pattern) {
                    $nodes->addChild('scope', '', ['name' => $name, 'pattern' => $pattern]);
                }
            })
            ->done()
        ;
    }

    public function getDefaultFileName(): string
    {
        return 'workspace.xml';
    }

    /**
     * @param string $root
     * @param string $target
     *
     * @return string
     */
    public function getScopesPattern(string $root, string $target): string
    {
        $relStr   = 'file[' . basename($root) . ']:';
        $patterns = [];

        // Include target root.
        $patterns[] = str_replace(
            search: "$relStr/",
            replace: $relStr,
            subject: UrlPathHelper::asProjectPath($target, $root, $relStr) . '//*',
        );

        // Exclude target vendor, node_modules
        foreach ([$target . '/vendor', $target . '/node_modules'] as $p) {
            if (file_exists($p)) {
                $patterns[] = '!' . str_replace(
                        search: "$relStr/",
                        replace: $relStr,
                        subject: UrlPathHelper::asProjectPath($p, $root, $relStr) . '//*',
                    );
            }
        }

        return implode('&&', $patterns);
    }

    public function getComponentName(): string
    {
        return 'NamedScopeManager';
    }

    public function getConfigSetup(): array
    {
        $config = $this->manager->getConfig()->getSetup($this->getDefaultConfigParam());

        // Currently, only 'true' is valid.
        return [$config];
    }

    public function getDefaultConfigParam(): string
    {
        return 'appearanceAndBehavior.scopes';
    }

    /**
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $output = [];

        $xml       = $this->manager->getXml($this->getDefaultFileName());
        $component = $xml->query(NodeHelper::getComponentQuery($this->getComponentName()));
        $nodes     = $component->query('scope');

        foreach ($nodes as $node) {
            $output[] = [
                'name'    => $node->getAttribute('name'),
                'pattern' => $node->getAttribute('pattern'),
            ];
        }

        return $output;
    }
}
