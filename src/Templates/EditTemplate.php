<?php

namespace Chwnam\P3S\Templates;

use Closure;
use DOMElement;
use Exception;
use FluidXml\FluidContext;
use FluidXml\FluidXml;

class EditTemplate
{
    private int $index;

    private string $query;

    private ?Closure $notFoundCallback;

    private ?Closure $targetCallback;

    private bool $done;

    public function __construct(private readonly FluidXml $xml)
    {
        $this->index            = 0;
        $this->query            = '';
        $this->notFoundCallback = null;
        $this->targetCallback   = null;
        $this->done             = false;
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        if (!$this->done) {
            throw new Exception('EditTemplate not done! Check your code if done() is used or not.');
        }
    }

    public static function create(FluidXml $xml): self
    {
        return new self($xml);
    }

    public function doForTarget(Closure $callback): self
    {
        $this->targetCallback = $callback;

        return $this;
    }

    public function doWhenNodeNotFound(Closure $callback): self
    {
        $this->notFoundCallback = $callback;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function done(): void
    {
        $this->done = true;

        if (!$this->query) {
            throw new Exception('Query is empty');
        }

        $nodes = $this->xml->query($this->query);

        if (0 === $nodes->size()) {
            if (!is_callable($this->notFoundCallback)) {
                throw new Exception('Node not found for query: ' . $this->query);
            }
            $nodes = call_user_func($this->notFoundCallback, $this->xml);
        } else {
            if ($this->index >= $nodes->size()) {
                throw new Exception('Index is out of range');
            }
        }

        if (!($nodes instanceof FluidContext)) {
            throw new Exception('Return of doWhenNodeNotFound must be FluidContext');
        }

        $node = $nodes[$this->index];

        if (!($node instanceof DOMElement)) {
            throw new Exception('Node is not DOMElement');
        }

        if (is_callable($this->targetCallback)) {
            call_user_func($this->targetCallback, $node, $nodes, $this->xml);
        }
    }

    public function findNode(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function setNodeIndex(int $index): self
    {
        $this->index = (int)abs($index);

        return $this;
    }
}