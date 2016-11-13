<?php

require_once '../includes/db_handler.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$user_id = NULL;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();

        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user_id = $db->getUserId($api_key);
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }   
}

/**
 * Listing all restaurants
 * method GET
 * url /restaurants          
 */
$app->get('/restaurants', function() {
            global $user_id;
            $response = array();
            $db = new DbHandler();

            // fetching all restaurants
            $result = $db->getAllRestaurants();

            $response["error"] = false;
            $response["resto"] = array();

            // looping through result and preparing tasks array
            while ($resto = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["resto_id"] = $resto["resto_id"];
                $tmp["resto_category"] = $resto["resto_category"];
                $tmp["resto_name"] = $resto["resto_name"];
                $tmp["resto_country"] = $resto["resto_country"];
                $tmp["resto_logo"] = $resto["resto_logo"];
                array_push($response["resto"], $tmp);
            }

            echoResponse(200, $response);
        });
/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

$app->run();
?>