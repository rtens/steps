<?php
namespace rtens\steps2\domain;

class Rating {
    /**
     * @var int
     */
    private $importance = 0;
    /**
     * @var int
     */
    private $urgency = 0;

    /**
     * @param [0;10] $importance
     * @param [0;10] $urgency
     */
    public function __construct($importance, $urgency) {
        $this->importance = $importance;
        $this->urgency = $urgency;
    }

    /**
     * @return [0;10]
     */
    public function getImportance() {
        return $this->importance;
    }

    /**
     * @return [0;10]
     */
    public function getUrgency() {
        return $this->urgency;
    }

    function __toString() {
        return $this->importance . 'i ' . $this->urgency . 'u';
    }
}