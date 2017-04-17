<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// Get watertension sensors' value
// hostdomain/threshold

function getThreshold() {
    $sql = "SELECT * FROM threshold ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $threshold = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($threshold);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getThresholdbyParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, name, lower_limit, upper_limit from threshold WHERE node_id = :node_id";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
}

function updateThreshold() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sensor = Slim::getInstance()->request()->get('sensor');
    $low = Slim::getInstance()->request()->get('low');
    $up = Slim::getInstance()->request()->get('up');

    if (isset($node_id) && isset($sensor) && isset($low)){
        //$sql = "UPDATE threshold SET lower_limit=:low WHERE node_id=:node_id AND name=:sensor";
        
        $sql = "INSERT INTO threshold (node_id, name, lower_limit) VALUES (:node_id, :sensor, :low) ON DUPLICATE KEY UPDATE node_id=:node_id, name=:sensor, lower_limit=:low";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("low", $low);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("sensor", $sensor);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($node_id) && isset($sensor) && isset($up)){
        //$sql = "UPDATE threshold SET upper_limit=:up WHERE node_id=:node_id AND name=:sensor";
        $sql = "INSERT INTO threshold (node_id, name, upper_limit) VALUES (:node_id, :sensor, :up) ON DUPLICATE KEY UPDATE node_id=:node_id, name=:sensor, upper_limit=:up";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("up", $up);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("sensor", $sensor);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($node_id) && isset($sensor) && isset($low) && isset($up)){
        //$sql = "UPDATE threshold SET lower_limit=:low, upper_limit=:up WHERE node_id=:node_id AND name=:sensor";
        $sql = "INSERT INTO threshold (node_id, name, lower_limit, upper_limit) VALUES (:node_id, :sensor, :low, :up) ON DUPLICATE KEY UPDATE node_id=:node_id, name=:sensor, lower_limit=:low, upper_limit=:up";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("low", $low);
            $stmt->bindParam("up", $up);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("sensor", $sensor);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
}