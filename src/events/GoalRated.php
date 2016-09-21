<?php namespace rtens\steps\events;
use rtens\steps\model\GoalIdentifier;

class GoalRated {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var float
     */
    private $importance;
    /**
     * @var float
     */
    private $urgency;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param float $importance
     * @param float $urgency
     * @param \DateTime $when
     */
    public function __construct(GoalIdentifier $goal, $importance, $urgency, \DateTime $when) {
        $this->goal = $goal;
        $this->importance = $importance;
        $this->urgency = $urgency;
        $this->when = $when;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return float
     */
    public function getImportance() {
        return $this->importance;
    }

    /**
     * @return float
     */
    public function getUrgency() {
        return $this->urgency;
    }
}