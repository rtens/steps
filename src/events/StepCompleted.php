<?php namespace rtens\steps\events;
use rtens\steps\model\StepIdentifier;

class StepCompleted {
    /**
     * @var StepIdentifier
     */
    private $step;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param StepIdentifier $step
     * @param \DateTime $when
     */
    public function __construct(StepIdentifier $step, \DateTime $when) {
        $this->step = $step;
        $this->when = $when;
    }

    /**
     * @return StepIdentifier
     */
    public function getStep() {
        return $this->step;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}