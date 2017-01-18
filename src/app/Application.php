<?php
namespace rtens\steps2\app;

use rtens\domin\delivery\web\menu\ActionMenuItem;
use rtens\domin\delivery\web\WebApplication;
use rtens\udity\app\Application as Udity;

class Application extends Udity {

    public function run(WebApplication $ui, array $domainClasses) {
        $ui->setNameAndBrand('steps2');
        $ui->renderers->add(new CurrentStepRenderer($ui->renderers));

        $ui->menu->add(new ActionMenuItem('New', 'Goal$create'));
        $ui->menu->add(new ActionMenuItem('Goals', 'GoalList'));
        $ui->menu->add(new ActionMenuItem('Walk', 'Walk'));

        parent::run($ui, $domainClasses);
    }
}