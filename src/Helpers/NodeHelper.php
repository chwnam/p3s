<?php

namespace Chwnam\P3S\Helpers;

use Exception;
use FluidXml\FluidContext;
use FluidXml\FluidXml;

class NodeHelper
{
    public static function addComponent(FluidXml $xml, string $componentName): FluidContext
    {
        return $xml->addChild('component', '', ['name' => $componentName], true);
    }

    public static function getComponentQuery(string $componentName): string
    {
        return "/project[@version=\"4\"]/component[@name=\"$componentName\"]";
    }

    /**
     * @throws Exception
     */
    public static function queryOrGetNode(FluidContext $node, string $component): FluidContext
    {
        $component = trim($component, '/');
        $subnode   = $node->query("/$component");

        if (!$subnode->size()) {
            $pos = strrpos($component, '[');
            if (false !== $pos) {
                $component = substr($component, 0, $pos);
            }
            return $node->addChild($component, true);
        }

        return $subnode;
    }
}