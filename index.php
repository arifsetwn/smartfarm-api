<?php
header("Access-Control-Allow-Origin: *");

require "vendor/autoload.php";
use Slim\Slim;

$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json');

require_once('users.php');

// Routes untuk User
$app->get('/users', 'getUsers'); //works
$app->get('/user/:name',  'getUserByName'); //works
$app->get('/user/search/:query', 'findUserByName'); //works
$app->get('/user/update/by', 'updateUserBy');
// /user/update/by?username=xxx&name=xxx
// /user/update/by?username=xxx&name=xxx
$app->post('/user/add', 'addUser'); //works
$app->put('/user/update/:id', 'updateUser'); //not really works - tapi harus update semua data, nggak cuma satu variable
$app->put('/user/activate/:username', 'updateActvStatus');
$app->delete('/user/delete/:username', 'deleteUser'); //works



$app->run();

function getConnection() {
    $dbhost="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="smartfarm";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


?>
