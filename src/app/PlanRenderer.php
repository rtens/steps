<?php namespace rtens\steps\app;
use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\renderers\tables\types\DataTable;
use rtens\domin\delivery\web\renderers\tables\types\ObjectTable;
use rtens\domin\reflection\types\TypeFactory;
use rtens\steps\projecting\Plan;

class PlanRenderer extends TransformingRenderer {
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
        return $value instanceof Plan;
    }

    /**
     * @param Plan $plan
     * @return DataTable
     */
    protected function transform($plan) {
        $table = new ObjectTable($plan->getBlocks(), $this->types);
        $table->selectProperties(['units', 'goalName', 'nextStep']);
        $table->setHeader('goalName', 'Goal');
        $table->setHeader('units', 'Units (' . $plan->getUnits() . ')');

        return new DataTable($table);
    }
}