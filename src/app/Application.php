<?php namespace rtens\steps\app;

use rtens\domin\delivery\web\WebApplication;
use rtens\domin\reflection\ObjectActionGenerator;
use rtens\steps\model\Identifier;
use rtens\steps\model\Time;
use watoki\karma\implementations\commandQuery\CommandQueryApplication;
use watoki\karma\stores\EventStore;

class Application extends CommandQueryApplication {

    public static function sandbox() {
        Identifier::$makeUnique = false;
        Time::freeze(new \DateTime());

        return function (EventStore $store) {
            return new static($store);
        };
    }

    public function run(WebApplication $curir) {
        $curir->setNameAndBrand('steps');
        (new ObjectActionGenerator($curir->actions, $curir->types, $curir->parser))->fromFolder(__DIR__ . '/..', [$this, 'handle']);
    }
}