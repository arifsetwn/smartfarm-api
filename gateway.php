<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// Get watertension sensors' value
// hostdomain/gateways
function getGateways() {
    $sql = "SELECT * FROM gateway ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $gateways = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($gateways);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// hostdomain/gateways/:name
function getGatewayByName($name) {
    $sql = "SELECT gateway_id, name, location, description FROM gateway WHERE name=:name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $name);
        $stmt->execute();
        $gateway = $stmt->fetchObject();
        $db = null;
        echo json_encode($gateway);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// hostdomain/gateways/add
function addGateway() {
    $request = Slim::getInstance()->request();
    $gateway = json_decode($request->getBody());
    $sql = "INSERT INTO gateway (gateway_id, name, location, description) VALUES (:gateway_id, :name, :location, :description)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("gateway_id", $gateway->gateway_id);
        $stmt->bindParam("name", $gateway->name);
        $stmt->bindParam("location", $gateway->location);
        $stmt->bindParam("description", $gateway->description);
        $stmt->execute();
        $gateway->id = $db->lastInsertId();
        $db = null;
        echo "1";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function updateGatewayByName() {
    $gateway_id = Slim::getInstance()->request()->get('gateway_id');
    $name = Slim::getInstance()->request()->get('name');
    $location = Slim::getInstance()->request()->get('location');
    $description = Slim::getInstance()->request()->get('description');

    if (isset($gateway_id) && isset($name)){
        $sql = "UPDATE gateway SET name=:name WHERE gateway_id=:gateway_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("gateway_id", $gateway_id);
            $stmt->bindParam("name", $name);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($gateway_id) && isset($location)){
        $sql = "UPDATE triggers SET location=:location WHERE gateway_id=:gateway_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("gateway_id", $gateway_id);
            $stmt->bindParam("location", $location);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($gateway_id) && isset($description)){
        $sql = "UPDATE triggers SET description=:description WHERE gateway_id=:gateway_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("gateway_id", $gateway_id);
            $stmt->bindParam("description", $description);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }


}

// hostdomain/gateways/delete/:name
function deleteGateway($name) {
    $sql = "DELETE FROM gateway WHERE name=:name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $name);
        $stmt->execute();
        $db = null;
        echo "1";
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}