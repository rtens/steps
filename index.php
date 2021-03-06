<?php
require_once 'vendor/autoload.php';

use rtens\domin\delivery\web\adapters\curir\root\IndexResource;
use rtens\domin\delivery\web\WebApplication;
use rtens\steps2\app\Application;
use watoki\curir\WebDelivery;
use watoki\karma\stores\CachingEventStore;
use watoki\karma\stores\StoringEventStore;
use watoki\stores\stores\FileStore;

WebDelivery::quickResponse(IndexResource::class,
    WebDelivery::init(null,
        WebApplication::init(function (WebApplication $app) {
            (new Application(new CachingEventStore(new StoringEventStore(new FileStore('user/events')))))
                ->run($app, Application::loadClasses('src/domain'));
        })));