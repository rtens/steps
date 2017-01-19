<?php
namespace rtens\steps2\app;

use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\Element;
use rtens\domin\delivery\web\WebRenderer;

class TransformingRenderer implements WebRenderer {
    /**
     * @var RendererRegistry
     */
    private $renderers;
    /**
     * @var callable
     */
    private $handles;
    /**
     * @var callable
     */
    private $transform;

    public function __construct(RendererRegistry $renderers, callable  $handles, callable $transform) {
        $this->renderers = $renderers;
        $this->handles = $handles;
        $this->transform = $transform;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function handles($value) {
        return call_user_func($this->handles, $value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function render($value) {
        $transformed = $this->transform($value);
        return $this->renderers->getRenderer($transformed)->render($transformed);
    }

    /**
     * @param mixed $value
     * @return array|Element[]
     */
    public function headElements($value) {
        $transformed = $this->transform($value);
        $renderer = $this->renderers->getRenderer($transformed);

        if ($renderer instanceof WebRenderer) {
            return $renderer->headElements($transformed);
        }
        return [];
    }

    /**
     * @param $value
     * @return mixed
     */
    private function transform($value) {
        return call_user_func($this->transform, $value);
    }
}