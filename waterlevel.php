<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// http://localhost/slim_v2/waterlevel/data
function getWaterLevelData() {
    $sql = "SELECT node_id, time_stamp, water_level as value FROM water_level ORDER BY time_stamp";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($nodes);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

// ambil data yang masuk terakhir, nilai terkini
function getCurrentWaterLevelData() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, water_level FROM water_level WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY time_stamp";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getCurrentWaterLevelParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, water_level value from water_level WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY time_stamp";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getDailyWaterLevel() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, round(avg(water_level), 2) as value FROM water_level GROUP BY date";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getWeeklyWaterLevelData() {
    $sql = "SELECT node_id, date(time_stamp) date, week(time_stamp) week, round(avg(water_level), 2) value from water_level group by week";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getMonthlyWaterLevelData() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(water_level), 2) value from water_level group by month";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getThisHourWaterLevelData() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(water_level), 2) value FROM water_level WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR) group by ROUND(time_stamp/(120))";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getHourWaterLevelParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, hour(time_stamp) as hour, round(avg(water_level), 2) value from water_level WHERE node_id = :node_id group by date(time_stamp)";
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
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}

function getTodayWaterLevelData() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, round(avg(water_level), 2) as value FROM water_level WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 1 DAY) group by node_id, hour(time_stamp)";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getTodayWaterlevelDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, hour(time_stamp) as hour, round(avg(water_level), 2) value from water_level WHERE date(time_stamp)= curdate() group by node_id, hour(time_stamp)";
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
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}

function getThisWeekWaterLevelData() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(water_level), 2) value from water_level WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getThisWeekWaterlevelDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, round(avg(water_level), 2) value from water_level WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}

function getThisMonthWaterLevelData() {
    $sql = "SELECT date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(water_level), 2) value from water_level WHERE month(time_stamp) = month(NOW()) group by node_id, date(time_stamp)";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getMonthWaterLevelParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(water_level), 2) value from water_level WHERE node_id = :node_id AND month(time_stamp) = month(NOW()) group by node_id, date(time_stamp)";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getThisYearWaterLevelData() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(water_level), 2) value from water_level WHERE year(time_stamp) = year(NOW()) group by node_id, month(time_stamp)";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getThisYearWaterlevelDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(water_level), 2) value from water_level WHERE year(time_stamp) = year(NOW()) group by node_id, month(time_stamp)";
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
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

// By Param
function getWaterLevelbyParam() {
    $year = Slim::getInstance()->request()->get('year');
    $month = Slim::getInstance()->request()->get('month');
    $date = Slim::getInstance()->request()->get('date');
    $node_id = Slim::getInstance()->request()->get('node_id');

    // http://localhost/slim_v2/watertension?year=2016
    // search by year yyyy
    if (isset($year)) {
        $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(water_level), 2) value from water_level WHERE year(time_stamp) = :year group by node_id, month(time_stamp)";
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
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    // http://localhost/slim_v2/waterlevel?month=11
    // search by month mm
    if (isset($month)) { // month dalam dua digit
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(water_level), 2) value from water_level WHERE month(time_stamp) = :month group by node_id, date(time_stamp)";
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
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    // http://localhost/slim_v2/waterlevel?date=20161121
    // search by date yyyymmdd
    if (isset($date)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(water_level), 2) value from water_level WHERE DATE_FORMAT(time_stamp, '%Y%m%d') = :date group by node_id, hour(time_stamp)";
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
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    // http://localhost/slim_v2/waterlevel?node_id=anis2
    // search by node_id
    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(water_level), 2) value from water_level WHERE node_id = :node_id group by node_id, date(time_stamp)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->execute();
            $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($nodes);
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}