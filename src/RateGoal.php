<?php namespace rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\projecting\Goal;
use watoki\karma\implementations\commandQuery\Command;

class RateGoal implements Command {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var int
     */
    private $importance;
    /**
     * @var int
     */
    private $urgency;

    /**
     * @param GoalIdentifier $goal
     * @param [0;10] $importance
     * @param [0;10] $urgency
     */
    public function __construct(GoalIdentifier $goal, $importance, $urgency) {
        $this->goal = $goal;
        $this->importance = $importance;
        $this->urgency = $urgency;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return [0;10]
     */
    public function getImportance() {
        return $this->importance;
    }

    /**
     * @return [0;10]
     */
    public function getUrgency() {
        return $this->urgency;
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

    static public function fill(Application $app, $parameters) {
        if ($parameters['goal']) {
            /** @var Goal $goal */
            $goal = $app->handle(new ShowGoal($parameters['goal']));
            $parameters['importance'] = $goal->getImportance();
            $parameters['urgency'] = $goal->getUrgency();
        }
        return $parameters;
    }
}