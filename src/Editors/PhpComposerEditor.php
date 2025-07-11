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

readonly class PhpComposerEditor implements Editor
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

                if (!$enabled) {
                    return;
                }

                [
                    'synchronizationState' => $synchronizationState,
                    'pharConfigPath'       => $pharConfigPath,
                    'executablePath'       => $executablePath,
                ] = $this->getIdeaSetup();

                // Add synchronization attribute.
                if ('SYNCHRONIZE' !== $synchronizationState) {
                    $node->setAttribute('synchronizationState', 'SYNCHRONIZE');
                }

                if (!$executablePath) {
                    $executablePath = 'composer';
                }

                // Composer relative to projectPath
                $composerPath = UrlPathHelper::asProjectPath(
                    $this->manager->getConfig()->getTarget() . '/composer.json',
                    $this->manager->getConfig()->getProjectRoot(),
                );

                // Add <pharConfigPath>
                $pharConfigPathNode = NodeHelper::queryOrGetNode($nodes, 'pharConfigPath');
                if ($composerPath !== $pharConfigPathNode[0]->nodeValue) {
                    $pharConfigPathNode->text($composerPath);
                }

                // Add <execution>, <executable>
                $executionNode  = NodeHelper::queryOrGetNode($nodes, 'execution');
                $executableNode = $executionNode->query('/executable[@path]');
                if (!$executableNode->size()) {
                    $executableNode = $executionNode->addChild('executable', '', true);
                }
                if ($executablePath !== $executableNode->offsetGet(0)->getAttribute('path')) {
                    $executableNode->attr('path', $executablePath);
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
        return 'ComposerSettings';
    }

    public function getConfigSetup(): array
    {
        $setup = $this->manager->getConfig()->getSetup($this->getDefaultConfigParam());

        return [$setup];
    }

    public function getDefaultConfigParam(): string
    {
        return 'php.composer';
    }

    /**
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $output = [
            'synchronizationState' => '',
            'pharConfigPath'       => '',
            'executablePath'       => '',
        ];

        $xml   = $this->manager->getXml($this->getDefaultFileName());
        $nodes = $xml->query(NodeHelper::getComponentQuery($this->getComponentName()));

        if ($nodes->size()) {
            $node                           = $nodes->offsetGet(0);
            $output['synchronizationState'] = $node->getAttribute('synchronizationState');
            $output['pharConfigPath']       = $nodes->query('/pharConfigPath')->offsetGet(0)?->nodeValue;
            $output['executablePath']       = $nodes->query('/execution/executable')->offsetGet(0)?->getAttribute('path');
        }

        return $output;
    }
}
