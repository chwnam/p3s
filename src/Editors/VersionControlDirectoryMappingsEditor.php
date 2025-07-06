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
readonly class VersionControlDirectoryMappingsEditor implements Editor
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
            ->doForTarget(function (DOMElement $_, FluidContext $nodes) {
                [$jsonIncludes, $jsonExcludes] = $this->getConfigSetup();
                [$ideaIncludes, $ideaExcludes] = $this->getIdeaSetup();

                foreach ($jsonIncludes as $jsonItem) {
                    foreach ($ideaIncludes as $ideaItem) {
                        if ($jsonItem[0] === $ideaItem[0]) {
                            continue 2;
                        }
                    }
                    [$directory, $vcs] = $jsonItem;
                    $nodes->addChild('mapping', '', ['directory' => $directory, 'vcs' => $vcs]);
                }

                // $wNodes - workspace
                // Excludes - workspace
                $wXml  = $this->manager->getXml($this->getWorkspaceFileName());
                $wNode = $wXml->query(NodeHelper::getComponentQuery($this->getWorkspaceComponentName()));
                if (0 === $wNode->size()) {
                    $wNode = NodeHelper::addComponent($wXml, $this->getWorkspaceComponentName());
                }
                $wRoots = NodeHelper::queryOrGetNode($wNode, 'ignored-roots');

                // Append paths
                $diff = array_diff($jsonExcludes, $ideaExcludes);
                foreach ($diff as $item) {
                    $wRoots->addChild('path', '', ['value' => $item]);
                }
            })
            ->done()
        ;
    }

    /**
     * @return array{
     *      array<string, string>,
     *      string[],
     *  }
     *
     * @throws Exception
     */
    public function getConfigSetup(): array
    {
        $output = [
            [], // included
            [], // ignored
        ];

        $setup = $this->manager->getConfig()->getSetup('versionControl.directoryMappings');
        if ('preset:wordpress' !== $setup) {
            throw new Exception('Sorry, currently only preset:wordpress is supported.');
        }

        $projectRoot = $this->manager->getConfig()->getProjectRoot();
        if (!WordPress::isWordPress($projectRoot)) {
            throw new Exception('Sorry, the project root is not a WordPress installation, or wp-config.php is not found.');
        }

        $targetRoot = $this->manager->getConfig()->getTarget();
        if (UrlPathHelper::isGitRepo($targetRoot)) {
            $output[0][] = [
                UrlPathHelper::asProjectPath($targetRoot, $projectRoot), // path
                'Git',                                                   // type of vcs
            ];
        }

        if (WordPress::isPlugin($targetRoot)) {
            $plugins = glob(dirname($targetRoot) . '/*', GLOB_ONLYDIR) ?: [];
            foreach ($plugins as $item) {
                if ($item !== $targetRoot && UrlPathHelper::isGitRepo($item)) {
                    $output[1][] = UrlPathHelper::asProjectPath($item, $projectRoot);
                }
            }
            $themes = glob(dirname($targetRoot, 2) . '/themes/*', GLOB_ONLYDIR) ?: [];
            foreach ($themes as $item) {
                if (UrlPathHelper::isGitRepo($item)) {
                    $output[1][] = UrlPathHelper::asProjectPath($item, $projectRoot);
                }
            }
        } elseif (WordPress::isTheme($targetRoot)) {
            $plugins = glob(dirname($targetRoot) . '/*', GLOB_ONLYDIR) ?: [];
            foreach ($plugins as $item) {
                if (UrlPathHelper::isGitRepo($item)) {
                    $output[1][] = UrlPathHelper::asProjectPath($item, $projectRoot);
                }
            }
            $themes = glob(dirname($targetRoot, 2) . '/themes/*', GLOB_ONLYDIR) ?: [];
            foreach ($themes as $item) {
                if ($item !== $targetRoot && UrlPathHelper::isGitRepo($item)) {
                    $output[1][] = UrlPathHelper::asProjectPath($item, $projectRoot);
                }
            }
        }

        return $output;
    }

    /**
     * @return array{
     *     array<string, string>,
     *     string[],
     * }
     *
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $output = [
            [], // included
            [], // ignored
        ];

        $vcs       = $this->manager->getXml($this->getDefaultFileName());
        $workspace = $this->manager->getXml($this->getWorkspaceFileName());

        $mappings     = $vcs->query(NodeHelper::getComponentQuery($this->getComponentName()) . '/mapping');
        $ignoredRoots = $workspace->query(NodeHelper::getComponentQuery($this->getWorkspaceComponentName()) . '/ignored-roots/path');

        foreach ($mappings as $mapping) {
            $output[0][] = [
                $mapping->getAttribute('directory'),
                $mapping->getAttribute('vcs'),
            ];
        }

        foreach ($ignoredRoots as $ignoredRoot) {
            $output[1][] = $ignoredRoot->getAttribute('value');
        }

        return $output;
    }

    /**
     * Git ignored setup is stored in workspace.xml
     */
    public function getWorkspaceFileName(): string
    {
        return 'workspace.xml';
    }

    /**
     * Git setup
     *
     * @return string
     */
    public function getDefaultFileName(): string
    {
        return 'vcs.xml';
    }

    public function getDefaultConfigParam(): string
    {
        return 'versionControl.directoryMappings';
    }

    public function getComponentName(): string
    {
        return 'VcsDirectoryMappings';
    }

    public function getWorkspaceComponentName(): string
    {
        return 'VcsManagerConfiguration';
    }
}
