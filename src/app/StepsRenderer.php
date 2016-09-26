<?php namespace rtens\steps\app;
use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\renderers\tables\types\DataTable;
use rtens\domin\delivery\web\renderers\tables\types\ObjectTable;
use rtens\domin\reflection\types\TypeFactory;
use rtens\steps\projecting\Step;

class StepsRenderer  extends TransformingRenderer {
    /**
     * @var TypeFactory
     */
    private $types;

    /**
     * @param RendererRegistry $renderers
     * @param TypeFactory $types
     */
    public function __construct(RendererRegistry $renderers, TypeFactory $types) {
        parent::__construct($renderers);
        $this->types = $types;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function handles($value) {
        return is_array($value) && !empty($value) && $value[0] instanceof Step;
    }

    /**
     * @param Step[] $steps
     * @return DataTable
     */
    protected function transform($steps) {
        $table = new ObjectTable($steps, $this->types);
        $table->selectProperties(['description']);
        $table->setHeader('description', 'Step');

        return $table;
    }
}