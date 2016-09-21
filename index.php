<?php
require_once "vendor/autoload.php";

use rtens\domin\delivery\web\adapters\curir\root\IndexResource;
use rtens\domin\delivery\web\WebApplication;
use rtens\steps\app\Application;
use watoki\curir\WebDelivery;
use watoki\karma\stores\StoringEventStore;
use watoki\stores\stores\FileStore;

WebDelivery::quickResponse(IndexResource::class,
    WebDelivery::init(null,
        WebApplication::init(function (WebApplication $app) {
            (new Application(new StoringEventStore(new FileStore(__DIR__ . '/user/events'))))->run($app);
        })));