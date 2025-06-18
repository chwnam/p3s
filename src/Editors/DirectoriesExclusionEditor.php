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
readonly class DirectoriesExclusionEditor implements Editor
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
            ->findNode('/module[@type="WEB_MODULE"]/component[@name="NewModuleRootManager"]')
            ->doWhenNodeNotFound(function (FluidXml $xml): FluidContext {
                return $xml->addChild('component', '', ['name' => 'NewModuleRootManager'], true);
            })
            ->doForTarget(function (DomElement $node, FluidContext $nodes) {
                [$configContent, $configSources, $configExcludes] = $this->getConfigSetup();
                [, $ideaSource, $ideaExcludes] = $this->getIdeaSetup();

                // Find <content>
                $contentNode = NodeHelper::queryOrGetNode($nodes, '/content');
                $contentNode[0]->setAttribute('url', $configContent);

                // Add sourceFolder
                $sourceDiff = array_diff($configSources, $ideaSource);
                foreach ($sourceDiff as $source) {
                    $contentNode->addChild('sourceFolder', '', ['url' => $source, 'isTestSource' => 'false']);
                }

                // Add excludeFolder
                $excludeDiff = array_diff($configExcludes, $ideaExcludes);
                foreach ($excludeDiff as $exclude) {
                    $contentNode->addChild('excludeFolder', '', ['url' => $exclude]);
                }

                // Add orderEntry
                if (0 === $nodes->query('/orderEntry[@type="inheritedJdk"]')->size()) {
                    $nodes->addChild('orderEntry', '', ['type' => 'inheritedJdk']);
                }
                if (0 === $nodes->query('/orderEntry[@type="sourceFolder"]')->size()) {
                    $nodes->addChild('orderEntry', '', ['type' => 'sourceFolder', 'forTests' => 'false']);
                }
            })
            ->done()
        ;
    }

    public function getDefaultFileName(): string
    {
        $projectRoot = $this->manager->getConfig()->getProjectRoot();
        $basename    = basename($projectRoot);

        return "$basename.iml";
    }

    /**
     * @return array{
     *     string,   // content url
     *     string[], // source folters
     *     string[], // exclode folders
     * }
     */
    public function getConfigSetup(): array
    {
        $output = [
            'file://$MODULE_DIR$',  // content url
            [],                     // sourceFolders
            [],                     // excludeFolders
        ];

        $setup       = $this->manager->getConfig()->getSetup($this->getDefaultConfigParam());
        $target      = $this->manager->getConfig()->getTarget();
        $projectRoot = $this->manager->getConfig()->getProjectRoot();

        if ('preset:wordpress' !== $setup || !WordPress::isWordPress($projectRoot)) {
            return $output;
        }

        // Configuration is created by the WordPress directory structure.
        $pluginsDir = $projectRoot . '/wp-content/plugins';
        $themesDir  = $projectRoot . '/wp-content/themes';

        if (WordPress::isPlugin($target)) {
            // exclude plugins other than the target
            $plugins = glob($pluginsDir . '/*', GLOB_ONLYDIR) ?: [];
            foreach ($plugins as $plugin) {
                if ($plugin !== $target) {
                    $output[2][] = UrlPathHelper::asProjectPath($plugin, $projectRoot, 'file://$MODULE_DIR$');
                }
            }
            // exclude all themes
            $themes = glob($themesDir . '/*', GLOB_ONLYDIR) ?: [];
            foreach ($themes as $theme) {
                $output[2][] = UrlPathHelper::asProjectPath($theme, $projectRoot, 'file://$MODULE_DIR$');
            }
        } elseif (WordPress::isTheme($target)) {
            // exclude all plugins
            $plugins = glob($pluginsDir . '/*', GLOB_ONLYDIR) ?: [];
            foreach ($plugins as $plugin) {
                $output[2][] = UrlPathHelper::asProjectPath($plugin, $projectRoot, 'file://$MODULE_DIR$');
            }
            // exclude themes other than the target
            $themes = glob($themesDir . '/*', GLOB_ONLYDIR) ?: [];
            foreach ($themes as $theme) {
                if ($theme !== $target) {
                    $output[2][] = UrlPathHelper::asProjectPath($theme, $projectRoot, 'file://$MODULE_DIR$');
                }
            }
        } else {
            return $output;
        }

        $output[1][] = UrlPathHelper::asProjectPath($target, $projectRoot, 'file://$MODULE_DIR$');

        $otherExcludes = [
            $projectRoot . '/wp-content/upgrade',
            $projectRoot . '/wp-content/uploads',
        ];
        foreach ($otherExcludes as $exclude) {
            $output[2][] = UrlPathHelper::asProjectPath($exclude, $projectRoot, 'file://$MODULE_DIR$');
        }

        return $output;
    }

    /**
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $output = [
            '', // content url
            [], // sourceFolders
            [], // excludeFolders
        ];

        $xml           = $this->manager->getXml($this->getDefaultFileName());
        $componentNode = $xml->query('/component[@name="NewModuleRootManager"]');

        $content = $componentNode->query('/content');
        if ($content->size()) {
            $output[0] = $content[0]->getAttribute('url');

            $sourceFolders = $content->query('/sourceFolder');
            if ($sourceFolders->size()) {
                foreach ($sourceFolders as $sourceFolder) {
                    $output[1][] = $sourceFolder->getAttribute('url');
                }
            }

            $excludeFolders = $content->query('/excludeFolder');
            if ($excludeFolders->size()) {
                foreach ($excludeFolders as $excludeFolder) {
                    $output[2][] = $excludeFolder->getAttribute('url');
                }
            }
        }

        return $output;
    }

    public function getComponentName(): string
    {
        throw new Exception('The structure is irregular.');
    }

    public function getDefaultConfigParam(): string
    {
        return 'directories.exclusion';
    }
}
