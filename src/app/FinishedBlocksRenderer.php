<?php namespace rtens\steps\app;
use rtens\domin\delivery\RendererRegistry;
use rtens\domin\delivery\web\renderers\tables\types\DataTable;
use rtens\domin\delivery\web\renderers\tables\types\ObjectTable;
use rtens\domin\reflection\types\TypeFactory;
use rtens\steps\projecting\FinishedBlocks;

class FinishedBlocksRenderer extends TransformingRenderer {
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
        return $value instanceof FinishedBlocks;
    }

    /**
     * @param FinishedBlocks $blocks
     * @return DataTable
     */
    protected function transform($blocks) {
        $table = new ObjectTable($blocks->getBlocks(), $this->types);
        $table->selectProperties(['spentUnits', 'goalName', 'started', 'finished']);
        $table->setHeader('goalName', 'Goal');
        $table->setHeader('spentUnits', 'Units (' . round($blocks->getSpentUnits(), 1) . ')');

        $table->setFilter('spentUnits', function ($float) {
            return round($float, 2);
        });

        return new DataTable($table);
    }

}