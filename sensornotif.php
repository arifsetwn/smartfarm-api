<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

function updateSensorby(){
	$node_id = Slim::getInstance()->request()->get('node_id');
	$wind_speed = Slim::getInstance()->request()->get('wind_speed');
    $wind_direction = Slim::getInstance()->request()->get('wind_direction');
    $par = Slim::getInstance()->request()->get('par');
    $rainfall = Slim::getInstance()->request()->get('rainfall');
    $leaf_wetness = Slim::getInstance()->request()->get('leaf_wetness');
    $temperature = Slim::getInstance()->request()->get('temperature');
    $humidity = Slim::getInstance()->request()->get('humidity');
    $water_level = Slim::getInstance()->request()->get('water_level');
    $soil_temp = Slim::getInstance()->request()->get('soil_temp');
    $watertension = Slim::getInstance()->request()->get('watertension');

    if (isset($wind_speed)){
        //$sql = "UPDATE sensor_notification SET wind_speed=:wind_speed WHERE node_id=:node_id";
        //$sql = "INSERT INTO sensor_notification (node_id, wind_speed) VALUES (:node_id, :wind_speed)";
        $sql = "INSERT INTO sensor_notification (node_id, wind_speed) VALUES (:node_id, :wind_speed) ON DUPLICATE KEY UPDATE node_id=:node_id, wind_speed=:wind_speed";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("wind_speed", $wind_speed);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($wind_direction)){
        //$sql = "UPDATE sensor_notification SET wind_direction=:wind_direction WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, wind_direction) VALUES (:node_id, :wind_direction) ON DUPLICATE KEY UPDATE node_id=:node_id, wind_direction=:wind_direction";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("wind_direction", $wind_direction);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($par)){
        //$sql = "UPDATE sensor_notification SET par=:par WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, par) VALUES (:node_id, :par) ON DUPLICATE KEY UPDATE node_id=:node_id, par=:par";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("par", $par);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($rainfall)){
        //$sql = "UPDATE sensor_notification SET rainfall=:rainfall WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, rainfall) VALUES (:node_id, :rainfall) ON DUPLICATE KEY UPDATE node_id=:node_id, rainfall=:rainfall";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("rainfall", $rainfall);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($leaf_wetness)){
        //$sql = "UPDATE sensor_notification SET leaf_wetness=:leaf_wetness WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, leaf_wetness) VALUES (:node_id, :leaf_wetness) ON DUPLICATE KEY UPDATE node_id=:node_id, leaf_wetness=:leaf_wetness";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("leaf_wetness", $leaf_wetness);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($temperature)){
        //$sql = "UPDATE sensor_notification SET temperature=:temperature WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, temperature) VALUES (:node_id, :temperature) ON DUPLICATE KEY UPDATE node_id=:node_id, temperature=:temperature";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("temperature", $temperature);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($humidity)){
        //$sql = "UPDATE sensor_notification SET humidity=:humidity WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, humidity) VALUES (:node_id, :humidity) ON DUPLICATE KEY UPDATE node_id=:node_id, humidity=:humidity";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("humidity", $humidity);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($water_level)){
        //$sql = "UPDATE sensor_notification SET water_level=:water_level WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, water_level) VALUES (:node_id, :water_level) ON DUPLICATE KEY UPDATE node_id=:node_id, water_level=:water_level";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("water_level", $water_level);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($soil_temp)){
        //$sql = "UPDATE sensor_notification SET soil_temp=:soil_temp WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, soil_temp) VALUES (:node_id, :soil_temp) ON DUPLICATE KEY UPDATE node_id=:node_id, soil_temp=:soil_temp";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("soil_temp", $soil_temp);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($watertension)){
        //$sql = "UPDATE sensor_notification SET watertension=:watertension WHERE node_id=:node_id";
        $sql = "INSERT INTO sensor_notification (node_id, watertension) VALUES (:node_id, :watertension) ON DUPLICATE KEY UPDATE node_id=:node_id, watertension=:watertension";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("watertension", $watertension);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}

function getSensorStatus() {
	$node_id = Slim::getInstance()->request()->get('node_id');
	
	if (isset($node_id)){
        $sql = "SELECT * from sensor_notification WHERE node_id=:node_id";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->execute();
            $node = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($node);
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}