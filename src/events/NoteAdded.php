<?php namespace rtens\steps\events;
use rtens\steps\model\GoalIdentifier;

class NoteAdded {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var string
     */
    private $note;
    /**
     * @var \DateTime
     */
    private $when;

    /**
     * @param GoalIdentifier $goal
     * @param string $note
     * @param \DateTime $when
     */
    public function __construct(GoalIdentifier $goal, $note, \DateTime $when) {
        $this->goal = $goal;
        $this->note = $note;
        $this->when = $when;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return string
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * @return \DateTime
     */
    public function getWhen() {
        return $this->when;
    }
}