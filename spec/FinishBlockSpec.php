<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\FinishBlock;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Time;
use watoki\karma\testing\Specification;

class FinishBlockSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new FinishBlock(new BlockIdentifier('foo')));
        $this->then(new BlockFinished(new BlockIdentifier('foo'), Time::now()));
    }
}