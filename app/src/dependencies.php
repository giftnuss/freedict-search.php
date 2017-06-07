<?php
// DIC configuration

function dbg($arg)
{
    error_log(print_r($arg,1));
}

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $app_logger = new Apix\Log\Logger\File($settings['path']);
    $app_logger->setMinLevel($settings['level'])
           ->setCascading(false)     // don't propagate to further buckets
           ->setDeferred(false);     // postpone/accumulate logs processing

    return new Apix\Log\Logger( array($app_logger) );
};

$container['db'] = function ($c) {
    $settings = $c->get('settings');

    $db = Siox\Db::factory($settings['db']);

    $setup = new Freedict\Search\Setup($db);
    $setup->init();

    return $db;
};

$container['model'] = function ($c) {
    $db = $c->get('db');
    $schema = new Freedict\Search\Schema();
    return new Freedict\Search\Model($db,$schema);
};
