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

    public function now() {
        $this->when(new StartBlock(new BlockIdentifier('foo')));
        $this->then(new BlockStarted(new BlockIdentifier('foo'), Time::now()));
    }

    public function before() {
        $this->when(new StartBlock(new BlockIdentifier('foo'), new \DateTime('2011-12-13')));
        $this->then(new BlockStarted(new BlockIdentifier('foo'), new \DateTime('2011-12-13')));
    }
}