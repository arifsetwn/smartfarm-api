<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// http://localhost/slim_v2/soiltemp/data
function getSoilTempData() {
    $sql = "SELECT node_id, time_stamp, soil_temp as value FROM soil_temp ORDER BY time_stamp";
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

// http://localhost/slim_v2/soiltemp/current
// ambil data yang masuk terakhir, nilai terkini
function getCurrentSoilTempData() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, soil_temp as value FROM soil_temp WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY time_stamp";
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

function getCurrentSoilTempParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, soil_temp value from soil_temp WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) and node_id = :node_id ORDER BY time_stamp";
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

function getDailySoilTemp() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, round(avg(soil_temp), 2) as value FROM soil_temp GROUP BY date";
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

// http://localhost/slim_v2/soiltemp/weekly
function getWeeklySoilTempData() {
    $sql = "SELECT node_id, date(time_stamp) date, week(time_stamp) week, round(avg(soil_temp), 2) value from soil_temp group by week";
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

// http://localhost/slim_v2/soiltemp/monthly
function getMonthlySoilTempData() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(soil_temp), 2) value from soil_temp group by month";
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

// Grafik data per jam (per 30s)
// http://localhost/slim_v2/soiltemp/thishour
function getThisHourSoilTempData() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(soil_temp), 2) value FROM soil_temp WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR) group by ROUND(time_stamp/(120))";
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

function getHourSoilTempParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, hour(time_stamp) as hour, round(avg(soil_temp), 2) value from soil_temp WHERE node_id = :node_id group by date(time_stamp)";
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

// http://localhost/slim_v2/soiltemp/today
function getTodaySoilTempData() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, round(avg(soil_temp), 2) as value FROM soil_temp WHERE date(time_stamp)= curdate() group by node_id, hour(time_stamp)";
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

function getTodaySoiltempDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, hour(time_stamp) as hour, round(avg(soil_temp), 2) value from soil_temp WHERE date(time_stamp)= curdate() group by node_id, hour(time_stamp)";
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

// Data per minggu (per hari)
function getThisWeekSoilTempData() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(soil_temp), 2) value from soil_temp WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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

function getThisWeekSoiltempDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, round(avg(soil_temp), 2) value from soil_temp WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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

function getThisMonthSoilTempData() {
    $sql = "SELECT node_id, date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(soil_temp), 2) value from soil_temp WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH) group by node_id, date(time_stamp)";
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

function getMonthSoilTempParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(soil_temp), 2) value from soil_temp WHERE node_id = :node_id AND time_stamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH) group by node_id, month(time_stamp)";
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

function getThisYearSoilTempData() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(soil_temp), 2) value from soil_temp WHERE year(time_stamp) = year(NOW()) group by node_id, month(time_stamp)";
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

function getThisYearSoiltempDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(soil_temp), 2) value from soil_temp WHERE year(time_stamp) = year(NOW()) AND node_id = :node_id group by node_id, month(time_stamp)";
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
function getSoilTempbyParam() {
    $year = Slim::getInstance()->request()->get('year');
    $month = Slim::getInstance()->request()->get('month');
    $date = Slim::getInstance()->request()->get('date');
    $node_id = Slim::getInstance()->request()->get('node_id');

    // http://localhost/slim_v2/soiltemp?year=2016
    // search by year yyyy
    if (isset($year)) {
        $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(soil_temp), 2) value from soil_temp WHERE year(time_stamp) = :year group by node_id, month";
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

    // http://localhost/slim_v2/soiltemp?month=11
    // search by month mm
    if (isset($month)) { // month dalam dua digit
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(soil_temp), 2) value from soil_temp WHERE month(time_stamp) = :month group by node_id, date(time_stamp)";
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

    // http://localhost/slim_v2/soiltemp?date=20161121
    // search by date yyyymmdd
    if (isset($date)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(soil_temp), 2) value from soil_temp WHERE DATE_FORMAT(time_stamp, '%Y%m%d') = :date group by node_id, hour(time_stamp)";
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

    // http://localhost/slim_v2/soiltemp?node_id=anis2
    // search by node_id
    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(soil_temp), 2) value from soil_temp WHERE node_id = :node_id group by node_id, date(time_stamp)";
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