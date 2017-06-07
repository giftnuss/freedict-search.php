<?php

$app = require __DIR__ . '/../app/src/app.php';
$c = $app->getContainer();

$model = $c->get('model');

$english = $model->getLanguage('eng');
print_r($english);
