<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// hostdomain/battery/data
function getBatteryData() {
    $sql = "SELECT node_id, time_stamp, battery as value FROM battery ORDER BY time_stamp";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $battery = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($battery);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

// hostdomain/battery/current
// ambil data yang masuk terakhir, nilai terkini
function getCurrentBatteryData() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, battery as value FROM battery WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY time_stamp";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $battery = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="current";
        }
        echo json_encode($battery);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getCurrentBatteryParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, battery value from battery WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) and node_id = :node_id ORDER BY time_stamp";
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

// Mencari rata-rata nilai battery per hari
// localhost/battery/daily
function getDailyBatteryData() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, round(avg(battery), 2) as value FROM battery GROUP BY date";
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

// hostdomain/battery/weekly
function getWeeklyBatteryData() {
    $sql = "SELECT node_id, date(time_stamp) date, week(time_stamp) week, round(avg(battery), 2) value from battery group by week";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $battery = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="week";
        }
        echo json_encode($battery);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

// hostdomain/battery/monthly
function getMonthlyBatteryData() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(battery), 2) value from battery group by month";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $battery = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        foreach($nodes as $node){
            $node->timeblock="month";
        }
        echo json_encode($battery);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

// Grafik data per jam (per 30s)
// http://localhost/slim_v2/battery/thishour
function getThisHourBatteryData() {
    $sql = "SELECT node_id, date(time_stamp) date, DATE_FORMAT(time_stamp, '%h:%i') as hour, round(avg(battery), 2) value FROM battery WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR) group by ROUND(time_stamp/(120))";
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

function getHourBatteryParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, hour(time_stamp) as hour, round(avg(battery), 2) value from battery WHERE node_id = :node_id group by date(time_stamp)";
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

// hostdomain/battery/today
// rata-rata nilai data dalam satu hari, the last 24 hours data
function getTodayBatteryData() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, round(avg(battery), 2) as value FROM battery WHERE date(time_stamp)= curdate() group by node_id, hour";
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

function getTodayBatteryDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, hour(time_stamp) as hour, round(avg(battery), 2) value from battery WHERE date(time_stamp)= curdate() group by node_id, hour";
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

// Data per minggu (per hari)
// http://localhost/slim_v2/battery/thisweek
function getThisWeekBatteryData() {
    $sql = "SELECT node_id, date(time_stamp) date, round(avg(battery), 2) value from battery WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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

function getThisWeekBatteryDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) as date, round(avg(battery), 2) value from battery WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) group by node_id, date";
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

// http://localhost/slim_v2/battery/month
function getThisMonthBatteryData() {
    $sql = "SELECT node_id, date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(battery), 2) value from battery WHERE month(time_stamp) = month(NOW()) group by node_id, date(time_stamp)";
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

function getMonthBatteryParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, date(time_stamp) date, month(time_stamp) month, year(time_stamp) year, round(avg(battery), 2) value from battery WHERE node_id =:node_id AND month(time_stamp) = month(NOW()) group by node_id, date(time_stamp)";
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

// http://localhost/slim_v2/battery/thisyear
function getThisYearBatteryData() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(battery), 2) value from battery WHERE year(time_stamp) = year(NOW()) group by node_id, month(time_stamp)";
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

function getThisYearBatteryDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(battery), 2) value from battery WHERE year(time_stamp) = year(NOW()) AND node_id=:node_id group by node_id, month(time_stamp)";
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
function getBatterybyParam() {
    $year = Slim::getInstance()->request()->get('year');
    $month = Slim::getInstance()->request()->get('month');
    $date = Slim::getInstance()->request()->get('date');
    $node_id = Slim::getInstance()->request()->get('node_id');

    // http://localhost/slim_v2/battery?year=2016
    // search by year yyyy
    if (isset($year)) {
        $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(battery), 2) value from battery WHERE year(time_stamp) = :year group by node_id, month(time_stamp)";
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

    // http://localhost/slim_v2/battery?month=11
    // search by month mm
    if (isset($month)) { // month dalam dua digit
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(battery), 2) value from battery WHERE month(time_stamp) = :month group by node_id, date(time_stamp)";
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

    // http://localhost/slim_v2/battery?date=20161121
    // search by date yyyymmdd
    if (isset($date)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(battery), 2) value from battery WHERE DATE_FORMAT(time_stamp, '%Y%m%d') = :date group by node_id, hour(time_stamp)";
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

    // http://localhost/slim_v2/battery?node_id=anis2
    // search by node_id
    if (isset($node_id)) {
        $sql = "SELECT node_id, date(time_stamp) date, round(avg(battery), 2) value from battery WHERE node_id = :node_id group by node_id, date(time_stamp)";
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