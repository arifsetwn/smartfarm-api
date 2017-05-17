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

function authenticate(\Slim\Route $route) {

    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();


    if (isset($headers['Authorization'])) { //cek apakah ada header Authorization
        $db = new FunctionDB();
        $api_key = $headers['Authorization'];

        if (!$db->CekApiKey($api_key)) { //jika api key salah

            $response["message"] = "Api Key Salah";
            echoRespnse(401, $response);
            $app->stop();
        } else {

            return true; //kembalikan true jika api key benar

        }
    } else {

        $response["message"] = "Api Key tidak ditemukan"; //tidak ditemukan header api key
        echoRespnse(400, $response);
        $app->stop();
    }
}


//registrasi user
$app->post('/register', 'newRegister');

//login User
$app->post('/login','cekLogin');

//request api key baru
$app->get('/apikey/:id','authenticate','newApiKey');

//akses nodes
$app->get('/shownodes', 'authenticate','showNode');


/**
**  fungsi dibawah merupakan fungsi dari API sebelumnya
** yang ditambah dengan fungsi otentikasi token
**/

//$app->get('/alerts', 'authenticate','getAlerts');

$app->run();

?>
