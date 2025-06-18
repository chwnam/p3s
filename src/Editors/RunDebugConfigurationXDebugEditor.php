<?php

namespace Chwnam\P3S\Editors;

use Chwnam\P3S\EditManager;
use Chwnam\P3S\Helpers\NodeHelper;
use Chwnam\P3S\Templates\EditTemplate;
use DOMElement;
use Exception;
use FluidXml\FluidContext;
use FluidXml\FluidXml;

/**
 * Editor for setup.php.servers
 */
readonly class RunDebugConfigurationXDebugEditor implements Editor
{
    public function __construct(private EditManager $manager)
    {
    }

    /**
     * @return void
     *
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
            ->doForTarget(function (DOMElement $_, FluidContext $nodes): void {
                [$enabled, $hosts] = $this->getConfigSetup();
                $ideaSetup = $this->getIdeaSetup();

                if (!$enabled) {
                    return;
                }

                foreach (array_diff($hosts, $ideaSetup) as $host) {
                    $attrs = [
                        'name'        => 'Remote Debugging: ' . $host,
                        'type'        => 'PhpRemoteDebugRunConfigurationType',
                        'factoryName' => 'PHP Remote Debug',
                        'server_name' => $host,
                        'session_id'  => 'phpstorm-xdebug',
                    ];

                    $nodes
                        ->addChild('configuration', '', $attrs, true)
                        ->addChild('method', '', ['v' => '2'])
                    ;
                }
            })
            ->done()
        ;;
    }

    public function getDefaultFileName(): string
    {
        return 'workspace.xml';
    }

    /**
     * @throws Exception
     */
    public function getConfigSetup(): array
    {
        $enabled = $this->manager->getConfig()->getSetup($this->getDefaultConfigParam());
        $xml     = $this->manager->getXml($this->getDefaultFileName());

        // Extract servers list
        $hosts   = [];
        $servers = $xml->query('/project[@version="4"]/component[@name="PhpServers"]//server');
        foreach ($servers as $server) {
            $hosts[] = $server->getAttribute('host');
        }

        return [$enabled, $hosts];
    }

    /**
     * @return string[]
     *
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $output = [];

        $xml = $this->manager->getXml($this->getDefaultFileName());

        // Extract configurations
        $nodes = $xml->query('/project[@version="4"]/component[@name="RunManager"]/configuration[@type="PhpRemoteDebugRunConfigurationType"]');

        foreach ($nodes as $node) {
            $output[] = $node->getAttribute('server_name');
        }

        return $output;
    }

    public function getComponentName(): string
    {
        return 'RunManager';
    }

    public function getDefaultConfigParam(): string
    {
        return 'runDebugConfiguration.xdebug';
    }
}
