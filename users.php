<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// Functions untuk User
// hostdomain/users
function getUsers() {
    $sql = "SELECT * FROM user ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"sensor": ' . json_encode($users) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// hostdomain/users/:name
function getUserByName($name) {
    $sql = "SELECT name, address, field_location, FROM user WHERE name=:name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $name);
        $stmt->execute();
        $user = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($user);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// hostdomain/users/add
function addUser() {
    $request = Slim::getInstance()->request();
    $user = json_decode($request->getBody());

    $sql = "INSERT INTO user (username, password, email, name, address, field_location, activation_status) VALUES (:username, PASSWORD(:password), :email, :name, :address, :field_location, '0')";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $user->username);
        $stmt->bindParam("password", $user->password);
        $stmt->bindParam("email", $user->email);
        $stmt->bindParam("name", $user->name);
        $stmt->bindParam("address", $user->address);
        $stmt->bindParam("field_location", $user->field_location);
        $stmt->execute();
        $user->id = $db->lastInsertId();
        $db = null;
        echo "1";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
 
function updateUser($id) {
    $request = Slim::getInstance()->request();
    $body = $request->getBody();
    $user = json_decode($body);
    $sql = "UPDATE users SET username=:username, name=:name, pass=:pass, activation_status=:activation_status WHERE id=:id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $user->username);
        $stmt->bindParam("name", $user->name);
        $stmt->bindParam("pass", $user->pass);
        $stmt->bindParam("activation_status", $user->activation_status);
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $db = null;
        echo json_encode($user);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateUserBy(){
    $username = Slim::getInstance()->request()->get('username');
    $name = Slim::getInstance()->request()->get('name');
    $address = Slim::getInstance()->request()->get('address');
    $field_location = Slim::getInstance()->request()->get('field_location');

    if (isset($username) && isset($name)){
        $sql = "UPDATE user SET name=:name WHERE username=:username";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("username", $username);
            $stmt->bindParam("name", $name);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($username) && isset($address)){
        $sql = "UPDATE user SET address=:address WHERE username=:username";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("username", $username);
            $stmt->bindParam("address", $address);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($username) && isset($field_location)){
        $sql = "UPDATE user SET field_location=:field_location WHERE username=:username";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("username", $username);
            $stmt->bindParam("field_location", $field_location);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
}

// hostdomain/users/activate/:username
// belum bisa ngasih notif kalau username yang dimasukkan salah
function updateActvStatus($username) {
    $sql = "UPDATE user SET activation_status='1' WHERE username=:username";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $db = null;
        echo "1";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// hostdomain/users/delete/:id
function deleteUser($username) {
    $sql = "DELETE FROM user WHERE username=:username";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("username", $username);
        $stmt->execute();
        $db = null;
        echo "1";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// hostdomain/users/search/:query
function findUserByName($query) {
    $sql = "SELECT name, address, field_location FROM user WHERE UPPER(name) LIKE :query ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($users);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}