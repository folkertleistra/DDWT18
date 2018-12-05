<?php
/**
 * Controller
 * User: Folkert Leistra
 * Date: 4-12-18
 * Time: 12:00
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week3', 'ddwt18', 'ddwt18');

/* credentials for the authorized user */
$cred = set_cred('ddwt18', 'ddwt18');

/* Create Router instance */
$router = new \Bramus\Router\Router();
/* check the user credentials */
$router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
    if (!check_cred($cred)) {
        $feedback = [
            'type' => 'danger',
            'message' => 'Authentication for the user failed.'
        ];
        echo json_encode($feedback);
        http_response_code(401);
        exit();
    }
});

$router->mount('/api', function()  use ($router, $db) {
    http_content_type('application/json');

    /* GET for reading all series */
    $router->get('/series', function() use($db) {
        $series = get_series($db);
        $json_series = json_encode($series);
        echo $json_series;
    });

    /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) {
        $serie_info = get_serieinfo($db, $id);
        $json_series = json_encode($serie_info);
        echo $json_series;
    });

    /* Delete route for individual series */
    $router->post('/series/(\d+)', function($id) use($db) {
        $feedback = remove_serie($db, $id);
        echo json_encode($feedback);

    });
    /* Add series POST */
    $router->post('/series', function() use($db) {
        $feedback= add_serie($db, $_POST);
        echo json_encode($feedback);

    });
    /* update serie PUT */
    $router->put('/series/(\d+)', function($id) use($db) {
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        $feedback = update_serie($db, $serie_info);

        echo json_encode($feedback);
    });

    /* Fallback route if route doesn't exist */
    $router->set404(function() {
        header('HTTP/1.1 404 Not Found');
    });

});
/* Run the router */
$router->run();
