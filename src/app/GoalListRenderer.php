<?php namespace rtens\steps\app;
use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\renderers\tables\types\DataTable;
use rtens\domin\delivery\web\renderers\tables\types\ObjectTable;
use rtens\domin\reflection\types\TypeFactory;
use rtens\steps\projecting\GoalList;

class GoalListRenderer extends TransformingRenderer {
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
        return $value instanceof GoalList;
    }

    /**
     * @param GoalList $list
     * @return DataTable
     */
    protected function transform($list) {
        $table = new ObjectTable($list->getGoals(), $this->types);
        $table->selectProperties(['name', 'stepCount', 'nextStep', 'deadline', 'importance', 'urgency']);
        $table->setHeader('stepCount', 'Steps');

        return new DataTable($table);
    }
}