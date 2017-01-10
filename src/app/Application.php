<?php
namespace rtens\steps2\app;

use rtens\domin\delivery\web\WebApplication;
use rtens\udity\app\Application as Udity;

class Application extends Udity {

    public function run(WebApplication $ui, array $domainClasses) {
        parent::run($ui, $domainClasses);

        $ui->setNameAndBrand('steps2');
    }
}