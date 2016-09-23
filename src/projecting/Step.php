<?php namespace rtens\steps\projecting;
use rtens\steps\model\StepIdentifier;

class Step {
    /**
     * @var StepIdentifier
     */
    private $step;
    /**
     * @var string
     */
    private $description;
    /**
     * @var null|\DateTime
     */
    private $completed;

    /**
     * @param StepIdentifier $step
     * @param string $description
     */
    public function __construct(StepIdentifier $step, $description) {
        $this->step = $step;
        $this->description = $description;
    }

    /**
     * @return StepIdentifier
     */
    public function getStep() {
        return $this->step;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    public function setCompleted(\DateTime $when) {
        $this->completed = $when;
    }

    public function isCompleted() {
        return !!$this->completed;
    }
}