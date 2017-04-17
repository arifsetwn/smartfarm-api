<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

function getAirprData() {
    $sql = "SELECT node_id, time_stamp, air_pressure as value FROM air_pressure ORDER BY time_stamp";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getCurrentAirpr() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, air_pressure FROM air_pressure WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY time_stamp";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="current";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getCurrentAirprParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, air_pressure value from air_pressure WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) and node_id = :node_id ORDER BY time_stamp";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("node_id", $node_id);
        $stmt->execute();
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="current";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getDailyAirpr() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, round(avg(air_pressure), 2) as value FROM air_pressure GROUP BY date";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="day";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getWeeklyAirpr() {
    $sql = "SELECT node_id, date(time_stamp) date, week(time_stamp) week, round(avg(air_pressure), 2) value from air_pressure group by week";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="week";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getMonthlyAirpr() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(air_pressure), 2) value from air_pressure group by month";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="month";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getThisHourAirpr() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(air_pressure), 2) value FROM air_pressure WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR) group by ROUND(time_stamp/(120))";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="hour";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getHourAirprParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, hour(time_stamp) as hour, round(avg(air_pressure), 2) value from air_pressure WHERE node_id = :node_id group by date(time_stamp)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            foreach($nodes as $node){
                $node->timeblock="hour";
            }
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
}

function getTodayAirpr() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%h:%i') as hour, round(avg(air_pressure), 2) as value FROM air_pressure WHERE date(time_stamp)= curdate() group by hour(time_stamp)";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="day";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getTodayAirprDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, hour(time_stamp) as hour, round(avg(air_pressure), 2) value from air_pressure WHERE date(time_stamp)= curdate() group by hour(time_stamp)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            foreach($nodes as $node){
                $node->timeblock="day";
            }
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
}

function getThisWeekAirpr() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(air_pressure), 2) value from air_pressure WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="week";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getThisWeekAirprDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, round(avg(air_pressure), 2) value from air_pressure WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            foreach($nodes as $node){
                $node->timeblock="week";
            }
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
}

function getThisMonthAirpr() {
    $sql = "SELECT node_id, date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(air_pressure), 2) value from air_pressure WHERE month(time_stamp) = month(NOW()) group by date";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="month";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getMonthAirprParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(air_pressure), 2) value from air_pressure WHERE node_id = :node_id AND month(time_stamp) = month(NOW()) group by date(time_stamp)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("node_id", $node_id);
        $stmt->execute();
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="month";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getThisYearAirpr() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(air_pressure), 2) value from air_pressure WHERE year(time_stamp) = year(NOW()) group by month(time_stamp)";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="year";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getThisYearAirprDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(air_pressure), 2) value from air_pressure WHERE year(time_stamp) = year(NOW()) AND node_id = :node_id group by month(time_stamp)";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("node_id", $node_id);
        $stmt->execute();
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="year";
        }
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

// By Param
function getAirprbyParam() {
    $year = Slim::getInstance()->request()->get('year');
    $month = Slim::getInstance()->request()->get('month');
    $date = Slim::getInstance()->request()->get('date');
    $node_id = Slim::getInstance()->request()->get('node_id');

    // http://localhost/slim_v2/watertension?year=2016
    // search by year yyyy
    if (isset($year)) {
        $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(air_pressure), 2) value from air_pressure WHERE year(time_stamp) = :year group by month";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("year", $year);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            foreach($nodes as $node){
                $node->timeblock="year";
            }
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    // http://localhost/slim_v2/watertension?month=11
    // search by month mm
    if (isset($month)) { // month dalam dua digit
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(air_pressure), 2) value from air_pressure WHERE month(time_stamp) = :month group by date(time_stamp)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("month", $month);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            foreach($nodes as $node){
                $node->timeblock="month";
            }
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    // http://localhost/slim_v2/watertension?date=20161121
    // search by date yyyymmdd
    if (isset($date)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(air_pressure), 2) value from air_pressure WHERE DATE_FORMAT(time_stamp, '%Y%m%d') = :date group by hour(time_stamp)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("date", $date);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            foreach($nodes as $node){
                $node->timeblock="day";
            }
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    // http://localhost/slim_v2/watertension?node_id=anis2
    // search by node_id
    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(air_pressure), 2) value from air_pressure WHERE node_id = :node_id group by date(time_stamp)";
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