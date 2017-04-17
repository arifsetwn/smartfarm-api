<?php
require_once '../include/UserDB.php';
require_once '../include/PassHash.php';

require '../vendor/autoload.php';

use Slim\Slim;
$app = new \Slim\Slim();

$app->post('/register', function() use ($app) {
            // verifikasi input
            verifyRequiredParams(array('name', 'username', 'email', 'password','role'));

            $response = array();

            // read post
            $name = $app->request->post('name');
            $username = $app->request->post('username');
            $email = $app->request->post('email');
            $password = $app->request->post('password');
            $role = $app->request->post('role');

            // validasi alamat email
            validateEmail($email);

            $db = new UserDB();
            $res = $db->createUser($name, $username, $email, $password, $role);

            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["message"] = "Kamu Berhasil terdaftar";
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Eror saat mendaftar";
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Email kamu sudah terdaftar";
            }

            //echo error
            echoRespnse(201, $response);
        });

$app->post('/login', function() use ($app){
        verifyRequiredParams(array('email','password')) ;

        $email = $app->request()->post('email');
        $password = $app->request()->post('password');
        $response = array();

        $db = new UserDB();

        if ($db->checkLogin($email,$password)){
            $user = $db->getUserByEmail($email);

            if ($user != NULL) {
                $response["error"] = false;
                $response['name'] = $user['name'];
                $response['username'] = $user['username'];
                
                $response['apiKey'] = $user['api_key'];
                $response['activation_status'] = $user['activation_status'];
                $response['createdAt'] = $user['created_at'];
                $response['role'] = $user['role'];
            } else {
                // unknown error occurred
                $response['error'] = true;
                $response['message'] = "An error occurred. Please try again";
            }
        } else {
            // user credentials are wrong
            $response['error'] = true;
            $response['message'] = 'Login failed. Incorrect credentials';
        }

        echoRespnse(200, $response);

});



//contoh
$app->get('/foo', function () use ($app) {
    echo "You will see this...";
    $app->stop();
    echo "But not this";
});



/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

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
?>
