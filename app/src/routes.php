<?php
// Routes

$app->get('/', function ($request, $response, $args) {

    $db = $this->db;
    $args['db'] = $db;

    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/search/{arg:.*}', function ($request, $response, $args) {
    $db = $this->db;
    $model = $this->model;
    $translation = $model->search($args['arg']);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($translation));
});
