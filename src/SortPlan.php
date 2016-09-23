<?php namespace rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\projecting\Block;
use watoki\karma\implementations\commandQuery\Command;

class SortPlan implements Command {
    /**
     * @var array|BlockIdentifier[]
     */
    private $blocks;

    /**
     * @param BlockIdentifier[] $blocks
     */
    public function __construct(array $blocks) {
        $this->blocks = $blocks;
    }

    /**
     * @return array|BlockIdentifier[]
     */
    public function getBlocks() {
        return $this->blocks;
    }

    /**
     * @return mixed
     */
    public function getAggregateIdentifier() {
        return Steps::IDENTIFIER;
    }

    /**
     * @return object
     */
    public function getAggregateRoot() {
        return new Steps();
    }

    public static function fill(Application $app, $parameters) {
        /** @var \rtens\steps\projecting\Plan $plan */
        $plan = $app->handle(new ShowPlan());

        $parameters['blocks'] = array_values(array_map(function (Block $block) {
            return $block->getBlock();
        }, $plan->getBlocks()));

        return $parameters;
    }
}