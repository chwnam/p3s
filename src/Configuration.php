<?php

namespace Chwnam\Saops;

use Exception;
use stdClass;

class Configuration
{
    /**
     * @throws Exception
     */
    public function __construct(private array $conf)
    {
        $this->checkConfig();
    }

    /**
     * Check configuration
     *
     * @return void
     *
     * @throws Exception
     * @see config.dist.json
     */
    private function checkConfig(): void
    {
        if ('1.0' !== ($this->conf['version'] ?? '')) {
            throw new Exception('Currently, the version is fixed to 1.0.');
        }

        // target
        if (!isset($this->conf['target'])) {
            throw new Exception('`target` is not set.');
        }
        $this->conf['target'] = realpath($this->conf['target']);
        if (!$this->conf['target']) {
            throw new Exception('`target` is not valid.');
        }
        echo "Target: " . $this->conf['target'] . "\n";

        // projectRoot
        if (!isset($this->conf['projectRoot'])) {
            throw new Exception('`projectRoot` is not set.');
        }
        $this->conf['projectRoot'] = $this->getPath($this->conf['projectRoot']);

        if (!$this->conf['projectRoot']) {
            throw new Exception('`projectRoot` is empty.');
        }

        echo "ProjectRoot: " . $this->conf['projectRoot'] . "\n";

        $idea = $this->conf['projectRoot'] . '/.idea';
        if (
            !(file_exists($idea) && is_dir($idea) && is_readable($idea) && is_writable($idea) && is_executable($idea))
        ) {
            echo '`projectRoot` does not have .idea directory. Creating now.' . PHP_EOL;
            mkdir($idea, 0755);
        }

        $this->prepareIdeaFiles($idea);

        // setup
        if (!isset($this->conf['setup']) || !is_array($this->conf['setup'])) {
            $this->conf['setup'] = [];
        }
    }

    protected function getPath(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return realpath($path) ?? '';
        }

        return realpath($this->getTarget() . '/' . $path) ?? '';
    }

    private function prepareIdeaFiles(string $idea): void
    {
        $idea = realpath($idea);
        if (!$idea) {
            return;
        }

        $dir         = basename(dirname($idea));
        $xmlTemplate = '<?xml version="1.0" encoding="UTF-8"?><project version="4"/>';
        $imlTemplate = '<?xml version="1.0" encoding="UTF-8"?><module type="WEB_MODULE" version="4"/>';

        foreach (["$dir.iml", 'php.xml', 'vcs.xml', 'workspace.xml'] as $file) {
            $path = $idea . '/' . $file;
            if (!file_exists($path)) {
                if (str_ends_with($file, '.iml')) {
                    file_put_contents($path, $imlTemplate);
                } elseif (str_ends_with($file, '.xml')) {
                    file_put_contents($path, $xmlTemplate);
                }
            }
        }
    }

    public function getTarget(): string
    {
        return $this->conf['target'];
    }

    public function getProjectRoot(): string
    {
        return $this->conf['projectRoot'];
    }

    public function getSetup(string $setupName, $default = null)
    {
        $path  = explode('.', $setupName);
        $setup = $this->conf['setup'];

        foreach ($path as $p) {
            if (!isset($setup[$p])) {
                return $default;
            }
            $setup = $setup[$p];
        }

        return $setup;
    }

    /**
     * @throws Exception
     */
    public static function load(string $path): self
    {
        return new self(json_decode(file_get_contents($path), true) ?: []);
    }
}