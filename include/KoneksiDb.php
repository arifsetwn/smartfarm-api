<?php

class KoneksiDb {
  private $conn;

  function __construct(){

  }

//fungsi koneksi ke database
  function connect(){
    include_once dirname(__FILE__) . '/dbConfig.php';

    $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if (mysqli_connect_errno()){
      echo "koneksi dabatase gagal". mysqli_connect_error();
    }

    return $this->conn;
  }


  
}

?>
