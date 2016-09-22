<?php namespace spec\rtens\steps;

use rtens\steps\app\Application;
use rtens\steps\events\BlockFinished;
use rtens\steps\events\BlockStarted;
use rtens\steps\model\BlockIdentifier;
use rtens\steps\model\Steps;
use rtens\steps\projecting\FinishedBlocks;
use rtens\steps\ShowFinishedBlocks;
use watoki\karma\testing\Specification;

class ShowFinishedBlocksSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function none() {
        $this->when(new ShowFinishedBlocks());
        $this->then->returnShouldMatch(function (FinishedBlocks $blocks) {
            return $blocks->getBlocks() == [];
        });
    }

    public function one() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), new \DateTime('2011-12-13 12:00')));
        $this->given(new BlockFinished(new BlockIdentifier('foo'), new \DateTime('2011-12-13 13:00')));
        $this->when(new ShowFinishedBlocks());
        $this->then->returnShouldMatch(function (FinishedBlocks $blocks) {
            $block = $blocks->getBlocks()[0];
            return
                $block->getBlock() == new BlockIdentifier('foo')
                && $block->getStarted() == new \DateTime('2011-12-13 12:00')
                && $block->getFinished() == new \DateTime('2011-12-13 13:00')
                && $block->getUnits() == 3600 / Steps::UNIT_SECONDS;
        });
    }

    public function sumUnits() {
        $this->given(new BlockStarted(new BlockIdentifier('foo'), new \DateTime('2011-12-13 12:00')));
        $this->given(new BlockFinished(new BlockIdentifier('foo'), new \DateTime('2011-12-13 13:00')));
        $this->given(new BlockStarted(new BlockIdentifier('bar'), new \DateTime('2011-12-13 14:00')));
        $this->given(new BlockFinished(new BlockIdentifier('bar'), new \DateTime('2011-12-13 16:00')));
        $this->when(new ShowFinishedBlocks());

        $this->then->returnShouldMatch(function (FinishedBlocks $blocks) {
            return $blocks->getUnits() == $blocks->getBlocks()[0]->getUnits() + $blocks->getBlocks()[1]->getUnits();
        });
    }
}