<?php

namespace Chwnam\P3S\Editors;

use Chwnam\P3S\EditManager;
use Chwnam\P3S\Helpers\NodeHelper;
use Chwnam\P3S\Templates\EditTemplate;
use DOMElement;
use Exception;
use FluidXml\FluidContext;
use FluidXml\FluidXml;

readonly class PhpLanguageLevelEditor implements Editor
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
            ->doForTarget(function (DOMElement $node) {
                [$configVersion] = $this->getConfigSetup();
                [$ideaVersion] = $this->getIdeaSetup();
                if ($configVersion !== $ideaVersion) {
                    $node->setAttribute('php_language_level', $configVersion);
                }
            })
            ->done()
        ;
    }

    public function getDefaultFileName(): string
    {
        return 'php.xml';
    }

    /**
     * @throws Exception
     */
    public function getComponentName(): string
    {
        return 'PhpProjectSharedConfiguration';
    }

    public function getConfigSetup(): array
    {
        $value = $this->manager->getConfig()->getSetup($this->getDefaultConfigParam());

        return [$value];
    }

    public function getDefaultConfigParam(): string
    {
        return 'php.languageLevel';
    }

    /**
     * @throws Exception
     */
    public function getIdeaSetup(): array
    {
        $xml  = $this->manager->getXml($this->getDefaultFileName());
        $node = $xml->query(NodeHelper::getComponentQuery($this->getComponentName()));

        return [$node->offsetGet(0)->getAttribute('php_language_level')];
    }
}
