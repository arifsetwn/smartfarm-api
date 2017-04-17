<?php

class UserDB{
    private $conn;

    function __construct(){
      require_once dirname(__FILE__) . '/KoneksiDb.php';
      $db = new KoneksiDb();
      $this->conn = $db->connect();
    }

    //register user baru

    public function createUser($name, $username, $email, $password, $role){
      require_once 'PassHash.php';
      $response = array();

      //cek apakah user belum ada
      if (!$this->isUserExists($email)){

        $password_hash = PassHash::hash($password);

        $api_key = $this->generateApiKey();

        $insertquery = $this->conn->prepare("INSERT INTO user2(name, username, email, password, api_key, role,activation_status) values(?,?,?,?,?,?,1)");
        $insertquery->bind_param("ssssss", $name, $username, $email, $password_hash, $api_key, $role);

        $result = $insertquery->execute();
        $insertquery->close();

              if ($result) {
                return USER_CREATED_SUCCESSFULLY;
                }else {
                  return USER_CREATE_FAILED;
                }
          }
      else {
          return USER_ALREADY_EXISTED;
        }
        return $response;
      }

      //check login users
      public function checkLogin($email,$password) {

        $query = $this->conn->prepare("SELECT password FROM user2 WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $query->bind_result($password_hash);
        $query->store_result();

        if ($query->num_rows > 0){
          $query->fetch();
          $query->close();

            if(PassHash::check_password($password_hash, $password)){
              return TRUE;

            }else {
              return FALSE;
            }}
        else {
            $query->close();
            return FALSE;
        }
      }

      //check apakah email sudah ada
      private function isUserExists($email){
        $query = $this->conn->prepare("SELECT id from user2 WHERE email=?");
        $query->bind_param("s",$email);
        $query->execute();
        $query->store_result();
        $num_rows = $query->num_rows;
        $query->close();
        return $num_rows > 0;
      }

      //view user berdasar email
      public function getUserByEmail($email) {
        $query = $this->conn->prepare("SELECT name, username, api_key, activation_status, created_at, role FROM user2 WHERE email = ?");
        $query->bind_param("s", $email);
        if ($query->execute()) {
          $query->bind_result($name,$username, $api_key, $activation_status, $created_at, $role);
          $query->fetch();
          $user = array();
          $user["name"]=$name;
          $user["username"]=$username;
          
          $user["api_key"]=$api_key;
          $user["activation_status"]=$activation_status;
          $user["created_at"]=$created_at;
          $user["role"]=$role;
          $query->close();
          return $user;
        } else {
          return NULL;
        }
      }

      //melihat api key berdasar user id
      public function getApiKeyById($user_id){
        $query = $this->conn->prepare("SELECT api_key FROM user2 WHERE id = ?");
        $query->bind_param("i",$user_id);
        if ($query->execute()){
          $query->bind_result($api_key);
          $query->close();
          return $api_key;
        }else {
          return NULL;
        }
      }

      //melihat user id berdasar api key
      public function getUserId($api_key){
        $query = $this->conn->prepare("SELECT id from user2 WHERE api_key = ?");
        $query->bind_param("s", $api_key);
        if ($query->execute()){
            $query->bind_result($user_id);
            $query->fetch();
            $query->close();
            return $user_id;
        }else {
          return NULL;
        }
        }

      //cek validasi api key
      public function isValidApiKey($api_key){
        $query = $this->conn->prepare("SELECT id from user2 WHERE api_key = ?");
        $query->bind_param("s", $api_key);
        $query->execute();
        $query->store_result();
        $num_rows = $query->num_rows;
        $query->close();
        return $num_rows > 0;
      }

      //generate api key
      private function generateApiKey() {
        return md5(uniqid(rand(),true));
      }






}

 ?>
