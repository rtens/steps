<?php namespace rtens\steps;
use rtens\steps\projecting\CurrentBlock;
use watoki\karma\implementations\commandQuery\Query;

class ShowCurrentBlock implements Query {

    /**
     * @return object
     */
    public function getProjection() {
        return new CurrentBlock();
    }
}