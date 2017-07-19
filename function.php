  a<?php
  require_once 'include/functionDB.php';

  //fungsi registrasi user baru
  function newRegister() {
    $app = \Slim\Slim::getInstance();

    //verifikasi data yang masuk
    verifyRequiredParams(array('username', 'name', 'email', 'password','role'));

    //membaca input data
    $response = array();
    $username = $app->request->post('username');
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    $password = $app->request->post('password');
    $role = $app->request->post('role');

    //insert data ke database
    $db = new FunctionDB();
    $res = $db->createUser($username, $name, $email, $password, $role);

    //response dari database
    if ($res == 0 ) {
        $response["message"] = "Kamu Berhasil terdaftar";
    } else if ($res == 1) {
        $response["message"] = "Eror saat mendaftar";
    } else if ($res == 2) {
        $response["message"] = "Email kamu sudah terdaftar";
    }

    echoRespnse(201, $response);
}

function cekLogin(){
  $app = \Slim\Slim::getInstance();
  verifyRequiredParams(array('email','password')) ;

  $email = $app->request()->post('email');
  $password = $app->request()->post('password');
  $response = array();

  $db = new FunctionDB();
  if($db->prosesLogin($email,$password)){

   $user = $db->cekDatabyEmail($email); // load data dari db
   $api_key = $db->updateApiKey($user['id']); //ganti api key baru tiap login
   $expiry_time= $db->updateTime($user['id']); //update timestamp_expiry tiap login

      if ($user != NULL) {
          $response['id'] = $user['id'];
          $response['name'] = $user['name'];
          $response['username'] = $user['username'];
          $response['apiKey'] = $api_key;
          $response['activation_status'] = $user['activation_status'];
          $response['role'] = $user['role'];
          $response['expiry_time'] = $expiry_time;
      } else {

          $response['message'] = "Error tidak ditemukan data";
      }

  } else {
    $response['message'] = 'Login Gagal, Email / Password Salah';
  }

  echoRespnse(200, $response);

}

//fungsi verifikasi data dari header
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


function ShowNode() {
    $app = \Slim\Slim::getInstance();
    $app->contentType('application/json');

    $db = new functionDB();
    $headers = apache_request_headers();
    $response = array();

    $api_key = $headers['Authorization'];
    $user = $db->getUserRoleId($api_key);

    if ($user["role"]==0){
      $sql = "SELECT * FROM nodes ORDER BY name";
      try {
          $db = getConnection();
          $stmt = $db->query($sql);
          $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
          $db = null;
          echo '{"Nodes": ' . json_encode($nodes) . '}';
          }catch(PDOException $e) {
              echo '{"error":{"text":"'. $e->getMessage() .'"}}';
            }

    } else {
      $sql = "SELECT * from nodes WHERE user_id=:user_id OR status='public' ORDER BY name";
           try {
               $db = getConnection();
               $stmt = $db->prepare($sql);
               $stmt->bindParam("user_id", $user_id["id"]);
               $stmt->execute();
               $node = $stmt->fetchAll(PDO::FETCH_OBJ);
               $db = null;
               echo '{"Nodes": ' . json_encode($node) . '}';
           } catch(PDOException $e) {
               echo '{"error":{"text":"'. $e->getMessage() .'"}}';
           }

    }

}


function newApiKey($id){
//verifikasi token dulu apakah sesuai dengan token id

  $db = new functionDB();
  $app = \Slim\Slim::getInstance();
  $response = array();
  $api_key = $db->updateApiKey($id);
    if ($api_key != NULL){
          $response["message"] = 'Api Key Baru = ' . $api_key;
        } else {
          $response["message"] = "Error";
        }

  echoRespnse(400, $response);
}

function activate($id){
  $db = new functionDB();
  $app = \Slim\Slim::getInstance();
  $response = array();
  $aktif = $db->activateuser($id);
  if ($aktif != NULL){
        $response["status"] = 'Sukses';
      } else {
        $response["status"] = "Error";
      }

echoRespnse(400, $response);

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

//fungsi akses ke database menggunakan PDO
function getConnection() {
    $dbhost="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="smartfarm";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
