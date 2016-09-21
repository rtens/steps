<?php namespace rtens\steps\model;

class Goal {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var string
     */
    private $name;

    /**
     * @param GoalIdentifier $goal
     * @param string $name
     */
    public function __construct(GoalIdentifier $goal, $name) {
        $this->name = $name;
        $this->goal = $goal;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
}