<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\BlockStarted;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Time;
use rtens\steps\StartBlock;
use watoki\karma\testing\Specification;

class StartBlockSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new StartBlock(new BlockIdentifier('foo')));
        $this->then(new BlockStarted(new BlockIdentifier('foo'), Time::now()));
    }
}