<?php namespace rtens\steps\app;

use rtens\domin\delivery\web\renderers\link\types\ClassLink;
use rtens\domin\delivery\web\WebApplication;
use rtens\domin\reflection\GenericObjectAction;
use rtens\steps\model\Identifier;
use rtens\steps\model\Time;
use watoki\karma\implementations\commandQuery\CommandQueryApplication;
use watoki\karma\implementations\commandQuery\Query;
use watoki\karma\stores\EventStore;
use watoki\reflect\PropertyReader;
use watoki\reflect\type\ClassType;

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
        $curir->renderers->add(new PlanRenderer($curir->renderers, $curir->types));
    }

    private function registerActions(WebApplication $curir) {
        $linkedActions = [];

        foreach ($this->findClassesIn(__DIR__ . '/..') as $class) {
            $id = $this->makeActionId($class);
            $action = $this->makeAction($curir, $class);
            $action->generic()
                ->setModifying(!is_subclass_of($class, Query::class));

            $curir->actions->add($id, $action);

            $reader = new PropertyReader($curir->types, $class);
            foreach ($reader->readInterface() as $property) {
                $type = $property->type();
                if ($type instanceof ClassType && is_subclass_of($type->getClass(), Identifier::class)) {
                    $linkedActions[$type->getClass()][$id] = $property->name();
                }
            }
        }

        $this->linkActions($curir, $linkedActions);
    }

    private function findClassesIn($folder) {
        $before = get_declared_classes();

        foreach (glob($folder . '/*.php') as $file) {
            include_once($file);
        }

        return array_diff(get_declared_classes(), $before);
    }

    private function makeActionId($class) {
        return lcfirst((new \ReflectionClass($class))->getShortName());
    }

    private function makeAction(WebApplication $curir, $class) {
        return (new GenericObjectAction($class, $curir->types, $curir->parser, [$this, 'handle']));
    }

    private function linkActions(WebApplication $curir, $linkedActions) {
        foreach ($this->findClassesIn(__DIR__ . '/../projecting') as $projection) {
            $reader = new PropertyReader($curir->types, $projection);
            foreach ($reader->readInterface() as $property) {
                $type = $property->type();
                if ($type instanceof ClassType && array_key_exists($type->getClass(), $linkedActions)) {
                    foreach ($linkedActions[$type->getClass()] as $actionId => $propertyName) {
                        $curir->links->add(new ClassLink($projection, $actionId, function ($object) use ($property, $propertyName) {
                            return [$propertyName => ['key' => $property->get($object)]];
                        }));
                    }
                }
            }
        }
    }
}