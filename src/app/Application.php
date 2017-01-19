<?php
namespace rtens\steps2\app;

use rtens\domin\delivery\web\menu\ActionMenuItem;
use rtens\domin\delivery\web\renderers\tables\types\DataTable;
use rtens\domin\delivery\web\renderers\tables\types\ObjectTable;
use rtens\domin\delivery\web\WebApplication;
use rtens\steps2\domain\GoalList;
use rtens\udity\app\Application as Udity;

class Application extends Udity {

    public function run(WebApplication $ui, array $domainClasses) {
        $ui->setNameAndBrand('steps2');

        $ui->renderers->add(new CurrentStepRenderer($ui->renderers));
        $ui->renderers->add(new TransformingRenderer($ui->renderers,
            function ($value) {
                return $value instanceof GoalList;
            },
            function (GoalList $value) use ($ui) {
                return new DataTable((new ObjectTable($value->getList(), $ui->types))
                ->selectProperties(['parents', 'name']));
            }));

        $ui->menu->add(new ActionMenuItem('New', 'Goal$create'));
        $ui->menu->add(new ActionMenuItem('Goals', 'GoalList'));
        $ui->menu->add(new ActionMenuItem('Walk', 'Walk'));


        parent::run($ui, $domainClasses);
    }
}