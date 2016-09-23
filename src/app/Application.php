<?php namespace rtens\steps\app;

use rtens\domin\delivery\web\WebApplication;
use rtens\domin\reflection\GenericObjectAction;
use rtens\steps\model\Identifier;
use rtens\steps\model\Time;
use watoki\karma\implementations\commandQuery\CommandQueryApplication;
use watoki\karma\implementations\commandQuery\Query;
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
        $this->registerActions($curir);
        $curir->renderers->add(new GoalListRenderer($curir->renderers, $curir->types));
    }

    private function registerActions(WebApplication $curir) {
        foreach ($this->findActionsIn(__DIR__ . '/../*.php') as $class) {
            $curir->actions->add($this->makeActionId($class),
                $this->makeAction($curir, $class)->generic()
                    ->setModifying(!is_subclass_of($class, Query::class)));
        }
    }

    private function findActionsIn($folder) {
        $before = get_declared_classes();

        foreach (glob($folder) as $file) {
            include_once($file);
        }

        $newClasses = array_diff(get_declared_classes(), $before);
        return $newClasses;
    }

    private function makeActionId($class) {
        return lcfirst((new \ReflectionClass($class))->getShortName());
    }

    private function makeAction(WebApplication $curir, $class) {
        return (new GenericObjectAction($class, $curir->types, $curir->parser, [$this, 'handle']));
    }
}