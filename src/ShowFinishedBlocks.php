<?php namespace rtens\steps;
use rtens\steps\projecting\FinishedBlocks;
use watoki\karma\implementations\commandQuery\Query;

class ShowFinishedBlocks implements Query {
    /**
     * @var null|\DateTime
     */
    private $after;
    /**
     * @var null|\DateTime
     */
    private $before;

    /**
     * @param \DateTime|null $after
     * @param \DateTime|null $before
     */
    public function __construct(\DateTime $after = null, \DateTime $before = null) {
        $this->after = $after;
        $this->before = $before;
    }

    /**
     * @return object
     */
    public function getProjection() {
        return new FinishedBlocks($this->after, $this->before);
    }
}