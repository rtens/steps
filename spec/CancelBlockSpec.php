<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\CancelBlock;
use rtens\steps\events\BlockCancelled;
use rtens\steps\events\BlockStarted;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;
use watoki\karma\testing\Specification;

class CancelBlockSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new CancelBlock(new BlockIdentifier('foo')));
        $this->then(new BlockCancelled(new BlockIdentifier('foo'), Time::now()));
    }

    public function cannotCancelStartedBlock() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), Time::now()), Steps::IDENTIFIER);
        $this->when->tryTo(new CancelBlock(new BlockIdentifier('foo')));
        $this->then->shouldFail('Cannot cancel a block after it has been started.');
    }
}