<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

function GetAllCurrentData() {
    $sensors = [
        'temperature',
        'humidity',
        'soil_temp',
        'watertension',
        'water_level',
        'wind_speed',
        'pl_current',
        'battery',
        'leaf_wetness',
        'par',
        'wind_direction',
    ];
    
    try {
        $db = getConnection();
        $data = [];

        foreach ($sensors as $sensor) {
            $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, " . $sensor . " as value FROM " . $sensor . " ORDER BY time_stamp DESC LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $sensordata = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            foreach ($sensordata as $datum) {
                // Add additional property
                $datum->sensorType = $sensor;
                // Convert stdObject into array
                $datum = (array) $datum;
                // Push into $data to be returned later
                array_push($data, $datum);
            }
        }
        
        // Return joining data
        echo json_encode($data);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function GetAllCurrentDataBy() {
    // Get parameter
    $node_id = Slim::getInstance()->request()->get('node_id');
    $sensorType = Slim::getInstance()->request()->get('sensorType');

    $where = '';

    // Filter node_id
    if (isset($node_id)) {
        $where = "WHERE node_id = :node_id";
    }

    $sensors = [
            'temperature',
            'humidity',
            'soil_temp',
            'watertension',
            'water_level',
            'wind_speed',
            'pl_current',
            'battery',
            'leaf_wetness',
            'par',
            'wind_direction',
    ];
        
    if (isset($sensorType)) {
        // Overwrite
        $sensors = [ $sensorType ];
    }
    
        try {
            $db = getConnection();
            $data = [];

            foreach ($sensors as $sensor) {
                $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, $sensor as value FROM $sensor $where ORDER BY time_stamp DESC LIMIT 1";
                $stmt = $db->prepare($sql);
                $stmt->bindParam("node_id", $node_id);
                $stmt->execute();
                $sensordata = $stmt->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($sensordata as $datum) {
                    // Add additional property
                    $datum->sensorType = $sensor;
                    // Convert stdObject into array
                    $datum = (array) $datum;
                    // Push into $data to be returned later
                    array_push($data, $datum);
                }
            }
            
            // Return joining data
            echo json_encode($data);
        } catch(PDOException $e) {
            echo '{"error":{"text1":'. $e->getMessage() .'}}';
        }
}
    // $sensors = [
    //         'temperature',
    //         'humidity',
    //         'soil_temp',
    //         'watertension',
    //         'water_level',
    //         'wind_speed',
    //         'pl_current',
    //         'battery',
    //         'leaf_wetness',
    //         'par',
    //         'wind_direction',
    //     ];

    // if (isset($_GET[$sensorType])) {
    //     $sensors = array ($sensorType);
    // }
        
    // try {
    //     $db = getConnection();
    //     $data = [];

    //     foreach ($sensors as $sensor) {
    //         $sql = "SELECT node_id, DATE(time_stamp) as date, DATE_FORMAT(time_stamp, '%H:%i') as hour, " . $sensor . " as value FROM " . $sensor . " WHERE " . $sensor . " = :sensorType ORDER BY time_stamp DESC LIMIT 1";
    //         $stmt = $db->prepare($sql);
    //         $stmt->bindParam("sensorType", $sensorType);
    //         $stmt->execute();
    //         $sensordata = $stmt->fetchAll(PDO::FETCH_OBJ);
                    
    //         foreach ($sensordata as $datum) {
    //             // Add additional property
    //             $datum->sensorType = $sensor;
    //             // Convert stdObject into array
    //             $datum = (array) $datum;
    //             // Push into $data to be returned later
    //             array_push($data, $datum);
    //         }
    //     }
                
    //     // Return joining data
    //     echo json_encode($data);
    // } catch(PDOException $e) {
    //     echo '{"error":{"text2":'. $e->getMessage() .'}}';
    // }

