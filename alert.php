<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// hostdomain/alerts
// https://sawitfarm.com/api/alerts?page=10
function getAlerts() {
    $paging = Slim::getInstance()->request()->get('page');

    $limit = '';

    // Filter node_id
    if (isset($paging)) {
        $limit = "LIMIT $paging";
    }

    $sql = "SELECT * FROM alert ORDER BY time_stamp DESC $limit";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $rules = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($rules);
    } catch(PDOException $e) {
        echo '{"error1":{"text":'. $e->getMessage() .'}}';
    }
}

function getSensorAlert() {
    $sensor = Slim::getInstance()->request()->get('sensor');

    $sql = "SELECT time_stamp, sensor_name, node_id, value, status FROM alert WHERE sensor_name=:sensor ORDER BY time_stamp";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("sensor", $sensor);
        $stmt->execute();
        $rule = $stmt->fetchObject();
        $db = null;
        echo json_encode($rule);
    } catch(PDOException $e) {
        echo '{"error2":{"text":'. $e->getMessage() .'}}';
    }
}

function getCurrentAlert() {
    $sql = "SELECT time_stamp, sensor_name, node_id, value, status FROM alert ORDER BY time_stamp DESC LIMIT 1";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("sensor", $sensor);
        $stmt->execute();
        $rule = $stmt->fetchObject();
        $db = null;
        echo json_encode($rule);
    } catch(PDOException $e) {
        echo '{"error2":{"text":'. $e->getMessage() .'}}';
    }
}