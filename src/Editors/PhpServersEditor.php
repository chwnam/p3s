<?php

namespace Chwnam\P3S\Editors;

use Chwnam\P3S\EditManager;
use Chwnam\P3S\Helpers\NodeHelper;
use Chwnam\P3S\Helpers\UrlPathHelper;
use Chwnam\P3S\Presets\WordPress;
use Chwnam\P3S\Templates\EditTemplate;
use DOMElement;
use Exception;
use FluidXml\FluidContext;
use FluidXml\FluidXml;

/**
 * Editor for setup.php.servers
 */
readonly class PhpServersEditor implements Editor
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
                $serversNode = NodeHelper::queryOrGetNode($nodes, 'servers');
                $configItems = $this->getConfigSetup();
                $ideaItems   = $this->getIdeaSetup();

                // $nodes is <servers>.
                foreach (array_diff($configItems, $ideaItems) as $item) {
                    [$host, $port] = explode(':', $item, 2);

                    $attrs = ['host' => $host, 'id' => UrlPathHelper::getUuid4(), 'name' => $host];
                    if ($port && '80' !== $port) {
                        $attrs['port'] = $port;
                    }

                    $serversNode->addChild('server', '', $attrs);
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
        return 'PhpServers';
    }

    public function getConfigSetup(): array
    {
        $output  = [];
        $servers = $this->manager->getConfig()->getSetup($this->getDefaultConfigParam());

        foreach ($servers as $server) {
            if (is_array($server) && isset($server['host'], $server['port'])) {
                $output[] = UrlPathHelper::asServerInfo("{$server['host']}:{$server['port']}");
            } elseif (is_string($server)) {
                if (str_starts_with($server, 'preset:wordpress')) {
                    $output[] = WordPress::getServerInfo($server);
                } else {
                    $output[] = UrlPathHelper::asServerInfo($server);
                }
            }
        }

        return array_unique(array_filter($output));
    }

    public function getDefaultConfigParam(): string
    {
        return 'php.servers';
    }

    /**
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $output  = [];
        $xml     = $this->manager->getXml($this->getDefaultFileName());
        $servers = $xml->query(NodeHelper::getComponentQuery($this->getComponentName()))->query('//server');

        foreach ($servers as $server) {
            $host     = $server->getAttribute('host');
            $port     = $server->getAttribute('port');
            $output[] = UrlPathHelper::asServerInfo("$host:$port");
        }

        return $output;
    }
}
