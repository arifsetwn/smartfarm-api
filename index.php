<?php
/**
* File index utama
*
*
*/

require 'vendor/autoload.php';
require_once 'include/functionDB.php';
require_once 'function.php';

use Slim\Slim;

$app = new \Slim\Slim();

/**
** fungsi yang dikembangkan pada API ini
**/

function authenticate(\Slim\Route $route) {

    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();


    if (isset($headers['Authorization'])) { //cek apakah ada header Authorization
        $db = new FunctionDB();
        $api_key = $headers['Authorization'];
        $expiry_time= $db->CekExpiryTime($api_key); // cek expiry time


        if (!$db->CekApiKey($api_key)) { //jika api key salah
            $response["status"] = "Error";
            $response["message"] = "Api Key Salah";
            echoRespnse(401, $response);
            $app->stop();

        } elseif ( time() > $expiry_time ){ // jika waktu lewat
            $response["status"] = "Error";
            $response["message"] = "Timeout";
            $response["time_now"] = time();
            $response["time_expired"] = $expiry_time;
            echoRespnse(401, $response);
            $app->stop();
        }

        else {
            return true; //kembalikan true jika api key benar


        }
    } else {
        $response["status"] = "Error";
        $response["message"] = "Api Key tidak ditemukan"; //tidak ditemukan header api key
        echoRespnse(400, $response);
        $app->stop();
    }
}

function authenticateadmin(\Slim\Route $route) {

    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();


    if (isset($headers['Authorization'])) { //cek apakah ada header Authorization
        $db = new FunctionDB();
        $api_key = $headers['Authorization'];
        $expiry_time= $db->CekExpiryTime($api_key); // cek expiry time

        if (!$db->CekApiKey($api_key)) { //jika api key salah
            $response["status"] = "Error";
            $response["message"] = "Api Key Salah";
            echoRespnse(401, $response);
            $app->stop();
          } elseif ( time() > $expiry_time ){ // jika waktu lewat
              $response["status"] = "Error";
              $response["message"] = "Timeout";
              $response["time_now"] = time();
              $response["time_expired"] = $expiry_time;
              echoRespnse(401, $response);
              $app->stop();
          }else {

            $user = $db->getUserRoleId($api_key);
              if ($user["role"]==0){
                  return true;
              }
              else {
                $response["status"] = "Error";
                $response["message"] = "Anda bukan Admin";
                echoRespnse(401, $response);
                $app->stop();
              }
        }
    } else {
        $response["status"] = "Error";
        $response["message"] = "Api Key tidak ditemukan"; //tidak ditemukan header api key
        echoRespnse(400, $response);
        $app->stop();
    }
}



//registrasi user
$app->post('/register', 'newRegister');

//login User
$app->post('/login','cekLogin');


//aktivate user dibikin dulu
$app->get('/activate/:id','authenticateadmin','activate');

//akses nodes
$app->get('/shownodes', 'authenticate','showNode');


//request api key baru
//$app->get('/apikey/:id','authenticate','newApiKey');

/**
**  fungsi dibawah merupakan fungsi dari API sebelumnya
** yang ditambah dengan fungsi otentikasi token
**/

require_once('alert.php');
require_once('users.php');

//Routes untuk alert
$app->get('/alerts', 'authenticate','getAlerts');
$app->get('/alert/by', 'authenticate', 'getSensorAlert');
$app->get('/alert/current', 'authenticate', 'getCurrentAlert');

// Routes untuk User
$app->get('/users', 'authenticate', 'getUsers'); //works
$app->get('/user/:name','authenticate',  'getUserByName'); //works
$app->get('/user/search/:query','authenticate', 'findUserByName'); //works
$app->get('/user/update/by','authenticate', 'updateUserBy');
// /user/update/by?username=xxx&name=xxx
// /user/update/by?username=xxx&name=xxx
$app->post('/user/add','authenticate', 'addUser'); //works
$app->put('/user/update/:id','authenticate', 'updateUser'); //not really works - tapi harus update semua data, nggak cuma satu variable
$app->put('/user/activate/:username', 'authenticate','updateActvStatus');
$app->delete('/user/delete/:username', 'authenticate','deleteUser'); //works

$app->run();


?>
