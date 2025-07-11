<?php

namespace Chwnam\Saops\Editors;

use Chwnam\Saops\EditManager;
use Chwnam\Saops\Helpers\NodeHelper;
use Chwnam\Saops\Helpers\UrlPathHelper;
use Chwnam\Saops\Templates\EditTemplate;
use DOMElement;
use Exception;
use FluidXml\FluidContext;
use FluidXml\FluidXml;

/**
 * Editor for setup.php.servers
 */
readonly class PhpFrameworksWordPressEditor implements Editor
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
                $configSetup = $this->getConfigSetup();
                $ideaSetup   = $this->getIdeaSetup();

                if ($configSetup['enabled'] !== $ideaSetup['enabled']) {
                    $node->setAttribute('enabled', $configSetup['enabled'] ? 'true' : 'false');
                }

                if ($configSetup['installationPath'] !== $ideaSetup['installationPath']) {
                    NodeHelper::queryOrGetNode($nodes, 'wordpressPath')->text($configSetup['installationPath']);
                }
            })
            ->done()
        ;
    }

    public function getDefaultFileName(): string
    {
        return 'workspace.xml';
    }

    public function getComponentName(): string
    {
        return 'WordPressConfiguration';
    }

    public function getConfigSetup(): array
    {
        return $this->manager->getConfig()->getSetup($this->getDefaultConfigParam());
    }

    public function getDefaultConfigParam(): string
    {
        return 'php.frameworks.wordpress';
    }

    /**
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $output = [
            'enabled'          => false,
            'installationPath' => '',
        ];

        $xml       = $this->manager->getXml($this->getDefaultFileName());
        $component = $xml->query(NodeHelper::getComponentQuery($this->getComponentName()));

        if ($component->size()) {
            $output['enabled']          = 'true' === $component->offsetGet(0)->getAttribute('enabled');
            $output['installationPath'] = $component->query('/wordpressPath')->offsetGet(0)?->nodeValue;
        }

        return $output;
    }
}
