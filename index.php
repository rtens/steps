<?php
require_once "vendor/autoload.php";

use rtens\domin\delivery\web\adapters\curir\root\IndexResource;
use rtens\domin\delivery\web\WebApplication;
use watoki\curir\WebDelivery;

WebDelivery::quickResponse(IndexResource::class,
    WebDelivery::init(null,
        WebApplication::init(function (WebApplication $app) {
            $app->setNameAndBrand('steps');
            // Set-up $app here (e.g. $app->actions->add('foo', ...))
        })));