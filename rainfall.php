<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// function getRainfData() {
//     $sql = "SELECT node_id, time_stamp, pl_current as value FROM pl_current ORDER BY time_stamp";
//     try {
//         $db = getConnection();
//         $stmt = $db->query($sql);
//         $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
//         $db = null;
//         echo json_encode($nodes);
//     } catch(PDOException $e) {
//         echo '{"error":{"text":"'. $e->getMessage() .'"}}';
//     }
// }

// Fixed
function getCurrentRainf() {
    //$sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, pl_current FROM pl_current WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) ORDER BY time_stamp";
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, pl_current value FROM pl_current ORDER BY time_stamp DESC LIMIT 1";

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

// Fixed
function getCurrentRainfParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');
    //$sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, pl_current value from pl_current WHERE time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) and node_id = :node_id ORDER BY time_stamp";

    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, pl_current value from pl_current WHERE node_id = :node_id ORDER BY time_stamp DESC LIMIT 1";
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

function getDailyRainf() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, pl_day as value FROM pl_day GROUP BY date";
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

function getWeeklyRainf() {
    $sql = "SELECT node_id, date(time_stamp) date, week(time_stamp) week, round(avg(pl_day), 2) value from pl_day group by week";
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

function getMonthlyRainf() {
    $sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, round(avg(pl_day), 2) value from pl_day group by month";
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

// Fixed
function getThisHourRainf() {
    $sql = "SELECT node_id, date(time_stamp) date, pl_hour value FROM pl_hour ORDER BY time_stamp DESC LIMIT 1";
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

// Fixed
function getHourRainfParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        //$sql = "SELECT node_id, date(time_stamp) date, hour(time_stamp) as hour, avg(pl_current) value from pl_current WHERE node_id = :node_id AND time_stamp >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) group by date(time_stamp)";
        $sql = "SELECT node_id, date(time_stamp) date, pl_hour value FROM pl_hour ORDER BY time_stamp DESC LIMIT 1";
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

// Fixed
function getTodayRainf() {
    $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, round(pl_current, 2) as value FROM pl_current WHERE date(time_stamp)= curdate() GROUP BY node_id, hour";

    // $sql = "SELECT a.node_id, DATE(a.time_stamp) as date, DATE_FORMAT(a.time_stamp, '%h:%i') as hour, pl_hour as value from pl_hour a
    //         inner join (
    //             select max(time_stamp) as max 
    //             from pl_hour 
    //             group by hour(time_stamp)
    //         ) b
    //         on a.time_stamp = b.max";

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

// Fixed
function getTodayRainfallDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, round(pl_current, 2) as value FROM pl_current WHERE date(time_stamp)= curdate() AND node_id = :node_id GROUP BY node_id, hour";

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

// fixed
function getThisWeekRainf() {
    // $sql = "SELECT node_id, date(time_stamp) date, pl_day value from pl_day WHERE week(time_stamp) = week(NOW()) group by date(time_stamp)";
    $sql = "SELECT a.node_id, date(a.time_stamp) date, round(a.pl_day, 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY node_id, date";

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

// fixed
function getThisWeekRainfallDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    if (isset($node_id)) {
        $sql = "SELECT a.node_id, date(a.time_stamp) date, round(a.pl_day, 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE time_stamp >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY node_id, date";
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

function getThisMonthRainf() {
    $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) month, year(a.time_stamp) year, round(a.pl_day, 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE month(a.time_stamp) = month(NOW()) GROUP BY node_id, date(time_stamp)";
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

function getMonthRainfParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) as month, year(a.time_stamp) year, round(a.pl_day, 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE a.node_id = :node_id AND month(a.time_stamp) = month(NOW()) GROUP BY node_id, date(time_stamp)";
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

function getThisYearRainf() {
    //$sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, avg(pl_current) value from pl_current WHERE year(time_stamp) = year(NOW()) group by month(time_stamp)";

    $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) month, year(a.time_stamp) year, round(avg(a.pl_day), 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE year(a.time_stamp) = year(NOW()) group by node_id, month";

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

//
function getThisYearRainfallDataParam() {
    $node_id = Slim::getInstance()->request()->get('node_id');

    //$sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, avg(pl_current) value from pl_current WHERE year(time_stamp) = year(NOW()) AND node_id = :node_id group by month";

    $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) month, year(a.time_stamp) year, round(avg(a.pl_day), 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE year(a.time_stamp) = year(NOW()) AND node_id = :node_id group by node_id, month";

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
function getRainfbyParam() {
    $year = Slim::getInstance()->request()->get('year');
    $month = Slim::getInstance()->request()->get('month');
    $date = Slim::getInstance()->request()->get('date');
    $node_id = Slim::getInstance()->request()->get('node_id');

    // http://localhost/slim_v2/watertension?year=2016
    // search by year yyyy
    if (isset($year)) {
        //$sql = "SELECT node_id, year(time_stamp) year, month(time_stamp) month, avg(pl_current) value from pl_current WHERE year(time_stamp) = :year group by month";

        $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) month, year(a.time_stamp) year, round(avg(a.pl_day), 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE year(a.time_stamp) = :year group by node_id, month";
        
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

    // http://localhost/slim_v2/watertension?month=11
    // search by month mm
    if (isset($month)) { // month dalam dua digit
        $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) month, year(a.time_stamp) year, round(a.pl_day, 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE month(a.time_stamp) = :month group by node_id, date";
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

    // http://localhost/slim_v2/watertension?date=20161121
    // search by date yyyymmdd
    if (isset($date)) {
        //$sql = "SELECT node_id, date(time_stamp) date, avg(pl_current) value from pl_current WHERE DATE_FORMAT(time_stamp, '%Y%m%d') = :date group by hour(time_stamp)";

        $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) month, year(a.time_stamp) year, round(a.pl_day, 2) value from pl_hour a  
        inner join (
            select max(time_stamp) as max from pl_hour 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE DATE_FORMAT(a.time_stamp, '%Y%m%d') = :date GROUP BY node_id, hour(time_stamp)";

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

    // http://localhost/slim_v2/watertension?node_id=anis2
    // search by node_id
    if (isset($node_id)) {
        $sql = "SELECT a.node_id, date(a.time_stamp) date, month(a.time_stamp) month, year(a.time_stamp) year, round(a.pl_day, 2) value from pl_day a  
        inner join (
            select max(time_stamp) as max from pl_day 
            group by date(time_stamp)
        ) b on a.time_stamp = b.max WHERE node_id = :node_id GROUP BY node_id, date(time_stamp)";
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