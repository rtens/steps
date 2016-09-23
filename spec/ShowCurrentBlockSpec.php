<?php namespace spec\rtens\steps;
use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockPlanned;
use rtens\steps\events\BlockStarted;
use rtens\steps\events\GoalCreated;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use rtens\steps\projecting\CurrentBlock;
use rtens\steps\ShowCurrentBlock;
use watoki\karma\testing\Specification;

class ShowCurrentBlockSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function before() {
        $this->given(new GoalCreated(new GoalIdentifier('goal'), 'Goal', Time::now()));
        $this->given(new BlockPlanned(new BlockIdentifier('foo'), new GoalIdentifier('goal'), 1, Time::now()));
    }

    public function none() {
        $this->when(new ShowCurrentBlock());
        $this->then->returnShouldMatch(function (CurrentBlock $block) {
            return $block->getBlocks() == [];
        });
    }

    public function started() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), new \DateTime('2011-12-13')));
        $this->when(new ShowCurrentBlock());
        $this->then->returnShouldMatch(function (CurrentBlock $block) {
            return
                $block->getBlocks()[0]->getBlock() == new BlockIdentifier('foo')
                && $block->getBlocks()[0]->getStarted() == new \DateTime('2011-12-13');
        });
    }

    public function finished() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), Time::now()));
        $this->given(new BlockFinished(new BlockIdentifier('foo'), Time::now()));
        $this->when(new ShowCurrentBlock());
        $this->then->returnShouldMatch(function (CurrentBlock $block) {
            return $block->getBlocks() == [];
        });
    }
}