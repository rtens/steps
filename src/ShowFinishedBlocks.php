<?php namespace rtens\steps;
use rtens\steps\projecting\FinishedBlocks;
use watoki\karma\implementations\commandQuery\Query;

class ShowFinishedBlocks implements Query {

    /**
     * @return object
     */
    public function getProjection() {
        return new FinishedBlocks();
    }
}