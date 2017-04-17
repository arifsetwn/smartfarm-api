<?php
/**
* File index utama
*
*
*/

require_once 'include/UserDB.php';
require_once 'include/PassHash.php';

require 'vendor/autoload.php';

use Slim\Slim;

$app = new \Slim\Slim();
$user_id = NULL;

//require fungsi untuk user_idrequire_once('users.php');
require_once('nodes.php');
require_once('sensordata.php');
require_once('alert.php');
require_once('trigger.php');
require_once('gateway.php');
require_once('battery.php');
require_once('moistures.php');
require_once('soiltemp.php');
require_once('waterlevel.php');
require_once('humidity.php');
require_once('temperature.php');
require_once('leafwetness.php');
require_once('rainfall.php');
require_once('par.php');
require_once('winddirection.php');
require_once('windspeed.php');
require_once('airpressure.php');
require_once('other.php');
require_once('sensornotif.php');

//validasi api key
function authenticate(\Slim\Route $route) {

    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new UserDB();

        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
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
        echoRespnse(400, $response);
        $app->stop();
    }
}




//contoh hello world
$app->get('/hello', function () use ($app) {
    echo "Hello dab";

});

/**$app->get('/nodes', 'authenticate', function() use ($app){
  $sql = "SELECT * FROM nodes WHERE status='public' ORDER BY name";
  try {
      $db = getConnection();
      $stmt = $db->query($sql);
      $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
      echo '{"Nodes": ' . json_encode($nodes) . '}';
  } catch(PDOException $e) {
      echo '{"error":{"text":"'. $e->getMessage() .'"}}';
  }


});**/

$app->get('/nodes', 'authenticate', 'getNodes');

// Routes untuk Alert Rule
$app->get('/alerts', 'authenticate','getAlerts'); //https://sawitfarm.com/api/alerts?page=10
$app->get('/alert/by', 'authenticate', 'getSensorAlert');
$app->get('/alert/current', 'authenticate', 'getCurrentAlert');





/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}


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
