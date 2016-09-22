<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockStarted;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\CurrentBlock;
use rtens\steps\ShowCurrentBlock;
use watoki\karma\testing\Specification;

class ShowCurrentBlockSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function none() {
        $this->when(new ShowCurrentBlock());
        $this->then->returnShouldMatch(function (CurrentBlock $block) {
            return
                $block->getBlock() == null
                && $block->getStarted() == null;
        });
    }

    public function started() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), new \DateTime('2011-12-13')));
        $this->when(new ShowCurrentBlock());
        $this->then->returnShouldMatch(function (CurrentBlock $block) {
            return
                $block->getBlock() == new BlockIdentifier('foo')
                && $block->getStarted() == new \DateTime('2011-12-13');
        });
    }

    public function finished() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), Time::now()));
        $this->given(new BlockFinished(new BlockIdentifier('foo'), Time::now()));
        $this->when(new ShowCurrentBlock());
        $this->then->returnShouldMatch(function (CurrentBlock $block) {
            return
                $block->getBlock() == null
                && $block->getStarted() == null;
        });
    }
}