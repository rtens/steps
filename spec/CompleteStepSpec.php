<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\CompleteStep;
use rtens\steps\events\StepCompleted;
use rtens\steps\model\StepIdentifier;
use rtens\steps\model\Time;
use watoki\karma\testing\Specification;

class CompleteStepSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new CompleteStep(new StepIdentifier('foo')));
        $this->then(new StepCompleted(new StepIdentifier('foo'), Time::now()));
    }
}