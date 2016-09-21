<?php namespace spec\rtens\steps;
use rtens\domin\parameters\Html;
use rtens\steps\AddNote;
use rtens\steps\app\Application;
use rtens\steps\events\NoteAdded;
use rtens\steps\model\GoalIdentifier;
use rtens\steps\model\Time;
use watoki\karma\testing\Specification;

class AddNoteSpec extends Specification {

    public function __construct() {
        parent::__construct(Application::sandbox());
    }

    public function success() {
        $this->when(new AddNote(new GoalIdentifier('foo'), new Html('test')));
        $this->then(new NoteAdded(new GoalIdentifier('foo'), 'test', Time::now()));
    }
}