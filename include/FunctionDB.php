<?php

class FunctionDB{
    private $conn;

    function __construct(){
      require_once dirname(__FILE__) . '/KoneksiDb.php';
      $db = new KoneksiDb();
      $this->conn = $db->connect();
    }

    public function createUser($username, $name, $email, $password, $role){

      $response = array();

      //cek apakah user belum ada
      if (!$this->checkEmailUser($email)){

        $api_key = $this->generateApiKey();

        $insertquery = $this->conn->prepare("INSERT INTO user3(username, name, email, password, api_key, role,activation_status) values(?,?,?,?,?,?,0)");
        $insertquery->bind_param("ssssss", $username, $name, $email, $password, $api_key, $role);

        $result = $insertquery->execute();
        $insertquery->close();

              if ($result) {
                return 0; //berhasil insert data
                }else {
                  return 1; // error insert data
                }
          }
      else {
          return 2; //data sudah ada
        }
        return $response;
      }

      //generate api key
      private function generateApiKey() {
            return bin2hex(random_bytes(16));
      }


      //validasi email user dari database
      private function checkEmailUser($email){
        $query = $this->conn->prepare("SELECT id from user3 WHERE email=?");
        $query->bind_param("s",$email);
        $query->execute();
        $query->store_result();
        $num_rows = $query->num_rows;
        $query->close();
        return $num_rows > 0;
      }


      //verifikasi login

      public function prosesLogin($email,$password) {
          //get password dari db
          $query = $this->conn->prepare("SELECT password FROM user3 WHERE email = ?");
          $query->bind_param("s", $email);
          $query->execute();
          $query->bind_result($password_db);
          $query->store_result();

          //jika ditemukan password
          if ($query->num_rows > 0){
            $query->fetch();
            $query->close();

            if($password_db == $password){ //jika password cocok
                return TRUE;

              }else {
                return FALSE;
              }}
          else {
              $query->close();
              return FALSE;
          }
        }


      //cek data user berdasar email
      public function cekDatabyEmail($email) {
        $query = $this->conn->prepare("SELECT id, username, name, api_key, activation_status, role FROM user3 WHERE email = ?");
        $query->bind_param("s", $email);
        if ($query->execute()) {
          $query->bind_result($id, $username, $name, $api_key, $activation_status, $role);
          $query->fetch();

          $user = array();
          $user["id"]=$id;
          $user["username"]=$username;
          $user["name"]=$name;
          $user["api_key"]=$api_key;
          $user["activation_status"]=$activation_status;
          $user["role"]=$role;
          $query->close();
          return $user;
        } else {
          return NULL;
        }
      }


      //cek validasi api key
      public function CekApiKey($api_key){
        $query = $this->conn->prepare("SELECT id from user3 WHERE api_key = ?");
        $query->bind_param("s", $api_key);
        $query->execute();
        $query->store_result();
        $num_rows = $query->num_rows;
        $query->close();
        return $num_rows > 0;
      }

      //get user dan role
      public function getUserRoleid($api_key){
        $query = $this->conn->prepare("SELECT id,role from user3 WHERE api_key=?");
        $query->bind_param("s", $api_key);
        if ($query->execute()){
            $query->bind_result($user_id,$role);
            $query->fetch();
            $user = array();
            $user["id"]=$user_id;
            $user["role"]=$role;
            $query->close();
            return $user;
        }else {
          return NULL;
        }
      }

      //buat api key baru
      public function updateApiKey($id){
          $api_key = $this->generateApiKey();
          $query = $this->conn->prepare("UPDATE user3 SET api_key = ? WHERE id = ?");
          $query->bind_param("si",$api_key, $id);
              if ($query->execute()){
                return $api_key;
              }else {
                return NULL;
              }
      }

      //update timestamp_expiry baru
      public function updateTime($id){
          $expiry_time = time() + 600;
          $query = $this->conn->prepare("UPDATE user3 SET expiry_time = ? WHERE id = ?");
          $query->bind_param("si",$expiry_time, $id);
              if ($query->execute()){
                return $expiry_time;
              }else {
                return NULL;
              }
      }

      public function CekExpiryTime($api_key){
        $query = $this->conn->prepare("SELECT expiry_time from user3 WHERE api_key = ?");
        $query->bind_param("s", $api_key);
        $query->execute();
        $query->bind_result($expiry_time);
        $query->fetch();
        return $expiry_time;

      }

      //aktivate user
      public function activateuser($id){
        $query = $this->conn->prepare("UPDATE user3 SET activation_status = 1 WHERE id = ?");
        $query->bind_param("i",$id);
        if ($query->execute()){
          return true;
        }else {
          return NULL;
        }
      }

}

?>
