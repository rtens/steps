<?php namespace rtens\steps;
use rtens\domin\parameters\Html;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Steps;
use watoki\karma\implementations\commandQuery\Command;

class AddNote implements Command {
    /**
     * @var GoalIdentifier
     */
    private $goal;
    /**
     * @var Html
     */
    private $note;

    /**
     * @param GoalIdentifier $goal
     * @param Html $note
     */
    public function __construct(GoalIdentifier $goal, Html $note) {
        $this->goal = $goal;
        $this->note = $note;
    }

    /**
     * @return GoalIdentifier
     */
    public function getGoal() {
        return $this->goal;
    }

    /**
     * @return Html
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * @return string
     */
    public function getNoteContent() {
        return $this->note->getContent();
    }

    /**
     * @return mixed
     */
    public function getAggregateIdentifier() {
        return Steps::IDENTIFIER;
    }

    /**
     * @return object
     */
    public function getAggregateRoot() {
        return new Steps();
    }
}