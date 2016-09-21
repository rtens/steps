<?php namespace spec\rtens\steps;
use rtens\steps\AddSteps;
use rtens\steps\app\Application;
use rtens\steps\events\StepAdded;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\StepIdentifier;
use watoki\karma\testing\Specification;

class AddStepsSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function multiple() {
        $this->when(new AddSteps(new GoalIdentifier('foo'), [
            'one',
            'two'
        ]));
        $this->then(new StepAdded(new StepIdentifier('fooone'), new GoalIdentifier('foo'), 'one'));
        $this->then(new StepAdded(new StepIdentifier('footwo'), new GoalIdentifier('foo'), 'two'));
    }
}