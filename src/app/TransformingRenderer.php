<?php namespace rtens\steps\app;
use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\Element;
use rtens\domin\delivery\web\WebRenderer;

abstract class TransformingRenderer implements WebRenderer {
    /**
     * @var RendererRegistry
     */
    private $renderers;
    /**
     * @var mixed
     */
    private $cached;

    /**
     * @param RendererRegistry $renderers
     */
    public function __construct(RendererRegistry $renderers) {
        $this->renderers = $renderers;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    abstract protected function transform($value);

    /**
     * @param mixed $value
     * @return mixed
     */
    public function render($value) {
        $transformed = $this->transformAndCache($value);
        return $this->getRenderer($transformed)->render($transformed);
    }

    /**
     * @param mixed $value
     * @return array|Element[]
     */
    public function headElements($value) {
        $transformed = $this->transformAndCache($value);
        $renderer = $this->getRenderer($transformed);
        if (!($renderer instanceof WebRenderer)) {
            return [];
        }
        return $renderer->headElements($transformed);
    }

    /**
     * @param $value
     * @return WebRenderer
     * @throws \Exception
     */
    private function getRenderer($value) {
        return $this->renderers->getRenderer($value);
    }

    private function transformAndCache($value) {
        if (!$this->cached) {
            $this->cached = $this->transform($value);
        }
        return $this->cached;
    }
}