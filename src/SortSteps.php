<?php namespace rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\projecting\Step;
use watoki\karma\implementations\commandQuery\Command;

class SortSteps implements Command {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var array|StepIdentifier[]
     */
    private $steps;

    /**
     * @param GoalIdentifier $goal
     * @param StepIdentifier[] $steps
     */
    public function __construct(GoalIdentifier $goal, array $steps) {
        $this->goal = $goal;
        $this->steps = $steps;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return array|StepIdentifier[]
     */
    public function getSteps() {
        return $this->steps;
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
        /** @var \rtens\steps\projecting\Goal $goal */
        $goal = $app->handle(new ShowGoal($parameters['goal']));

        $parameters['steps'] = array_values(array_map(function (Step $step) {
            return $step->getStep();
        }, $goal->getSteps()));

        return $parameters;
    }
}