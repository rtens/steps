<?php namespace rtens\steps;

use rtens\steps\model\StepIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class CompleteStep implements Command {
    /**
     * @var StepIdentifier
     */
    private $step;

    /**
     * @param StepIdentifier $step
     */
    public function __construct(StepIdentifier $step) {
        $this->step = $step;
    }

    /**
     * @return StepIdentifier
     */
    public function getStep() {
        return $this->step;
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
}