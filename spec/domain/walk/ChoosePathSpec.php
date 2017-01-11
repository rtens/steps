<?php
namespace rtens\steps2\domain\walk;

use rtens\steps2\domain\Path;
use rtens\steps2\domain\PathIdentifier;
use rtens\steps2\domain\Walk;
use rtens\udity\check\DomainSpecification;
use rtens\udity\check\event\Events;
use rtens\udity\utils\Time;

class ChoosePathSpec extends DomainSpecification {

    function noPathChosen() {
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->getChosenPath(), null);
    }

    function chosePath() {
        $this->when(Walk::class)->handleChoosePath(new PathIdentifier('foo'));
        $this->then(Events::named('DidChoosePath')->with('path', new PathIdentifier('foo')))->shouldBeAppended();
    }

    function pathChosen() {
        $this->given(Path::class, 'foo')->created(Time::at('today'));
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->getChosenPath(), new PathIdentifier('foo'));
    }

    function chosenPathNotActive() {
        $this->given(Path::class, 'foo')->created(Time::at('tomorrow'));
        $this->givenThat('DidChoosePath', Walk::class)->with('path', new PathIdentifier('foo'));
        $this->whenProject(Walk::class)
            ->assertEquals($this->projection(Walk::class)->getChosenPath(), null);
    }
}