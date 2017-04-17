<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

function getLeafWetnessData() {
    $sql = "SELECT node_id, time_stamp, leaf_wetness as value FROM leaf_wetness ORDER BY time_stamp";
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

function getCurrentLeafWetness() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, leaf_wetness FROM leaf_wetness WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY time_stamp";
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

function getCurrentLeafWetnessParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, leaf_wetness value from leaf_wetness WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) and node_id = :node_id ORDER BY time_stamp";
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

function getDailyLeafWetness() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, round(avg(leaf_wetness), 2) as value FROM leaf_wetness GROUP BY date";
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

function getWeeklyLeafWetness() {
    $sql = "SELECT node_id, date(time_stamp) date, week(time_stamp) week, round(avg(leaf_wetness), 2) value from leaf_wetness group by week";
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

function getMonthlyLeafWetness() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(leaf_wetness), 2) value from leaf_wetness group by month";
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

function getThisHourLeafWetness() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(leaf_wetness), 2) value FROM leaf_wetness WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR) group by ROUND(time_stamp/(120))";
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

function getHourLeafWetnessParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, hour(time_stamp) as hour, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE node_id = :node_id group by date(time_stamp)";
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

function getTodayLeafWetness() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, round(avg(leaf_wetness), 2) as value FROM leaf_wetness WHERE date(time_stamp)= curdate() group by node_id, hour";
    
    //$sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, round(avg(leaf_wetness), 2) as value FROM leaf_wetness WHERE date(time_stamp) = curdate() group by hour";
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

function getTodayLeafwetnessDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, hour(time_stamp) as hour, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE date(time_stamp)= curdate() group by node_id, hour(time_stamp)";
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

function getThisWeekLeafWetness() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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

function getThisWeekLeafwetnessDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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

function getThisMonthLeafWetness() {
    $sql = "SELECT node_id, date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE month(time_stamp) = month(NOW()) group by node_id, date";
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

function getMonthLeafWetnessParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE node_id = :node_id AND month(time_stamp) = month(NOW()) group by node_id, date(time_stamp)";
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

function getThisYearLeafWetness() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE year(time_stamp) = year(NOW()) group by node_id, month(time_stamp)";
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

function getThisYearLeafwetnessDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, avg(leaf_wetness) value from leaf_wetness WHERE year(time_stamp) = year(NOW()) AND node_id = :node_id group by month(time_stamp)";

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
function getLeafWetnessbyParam() {
    $year = Slim::getInstance()->request()->get('year');
    $month = Slim::getInstance()->request()->get('month');
    $date = Slim::getInstance()->request()->get('date');
    $node_id = Slim::getInstance()->request()->get('node_id');

    // http://localhost/slim_v2/leaf_wetness?year=2016
    // search by year yyyy
    if (isset($year)) {
        $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE year(time_stamp) = :year group by node_id, month(time_stamp)";
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

    // http://localhost/slim_v2/leaf_wetness?month=11
    // search by month mm
    if (isset($month)) { // month dalam dua digit
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE month(time_stamp) = :month group by node_id, date(time_stamp)";
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

    // http://localhost/slim_v2/leaf_wetness?date=20161121
    // search by date yyyymmdd
    if (isset($date)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE DATE_FORMAT(time_stamp, '%Y%m%d') = :date group by node_id, hour(time_stamp)";
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

    // http://localhost/slim_v2/leaf_wetness?node_id=anis2
    // search by node_id
    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(leaf_wetness), 2) value from leaf_wetness WHERE node_id = :node_id group by node_id, date(time_stamp)";
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