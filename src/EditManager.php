<?php

namespace Chwnam\P3S;

use Exception;
use FluidXml\FluidXml;
use RuntimeException;

class EditManager
{
    private Configuration $config;

    private array $docs = [];

    /**
     * @throws Exception
     */
    public function __construct(string $configPath = '')
    {
        if (!empty($configPath)) {
            $config = Configuration::load($configPath);
            $this->setConfig($config);
        }
    }

    public function finish(): void
    {
        foreach ($this->docs as $fileName => $xml) {
            file_put_contents($this->getIdeaPath() . '/' . $fileName, $xml->xml());
        }
    }

    public function getConfig(): Configuration
    {
        return $this->config;
    }

    public function setConfig(Configuration $config): void
    {
        $this->config = $config;
    }

    /**
     * @throws Exception
     */
    public function getXml(string $fileName): FluidXml
    {
        if (!isset($this->docs[$fileName])) {
            $path = $this->getIdeaPath() . '/' . $fileName;
            if (!file_exists($path) || !(is_file($path) && is_readable($path) && is_writable($path))) {
                throw new RuntimeException('File not found: ' . $fileName);
            }
            $xml = FluidXml::load($path);

            $this->docs[$fileName] = $xml;
        } else {
            $xml = $this->docs[$fileName];
        }

        return $xml;
    }

    private function getIdeaPath(): string
    {
        return $this->config->getProjectRoot() . '/.idea';
    }
}
