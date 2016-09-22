<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockStarted;
use rtens\steps\FinishBlock;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\model\Time;
use watoki\karma\testing\Specification;

class FinishBlockSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function now() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), Time::now()), Steps::IDENTIFIER);
        $this->when(new FinishBlock(new BlockIdentifier('foo')));
        $this->then(new BlockFinished(new BlockIdentifier('foo'), Time::now()));
    }

    public function history() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), Time::now()), Steps::IDENTIFIER);
        $this->when(new FinishBlock(new BlockIdentifier('foo'), new \DateTime('2011-12-13')));
        $this->then(new BlockFinished(new BlockIdentifier('foo'), new \DateTime('2011-12-13')));
    }

    public function notStarted() {
        $this->when->tryTo(new FinishBlock(new BlockIdentifier('foo')));
        $this->then->shouldFail('No block was started.');
    }

    public function notCurrent() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), Time::now()), Steps::IDENTIFIER);
        $this->when->tryTo(new FinishBlock(new BlockIdentifier('bar')));
        $this->then->shouldFail('This is not the current block.');
    }

    public function finished() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), Time::now()), Steps::IDENTIFIER);
        $this->given(new BlockFinished(new BlockIdentifier('foo'), Time::now()), Steps::IDENTIFIER);
        $this->when->tryTo(new FinishBlock(new BlockIdentifier('foo')));
        $this->then->shouldFail('No block was started.');
    }
}