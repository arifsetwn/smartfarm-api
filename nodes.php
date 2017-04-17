<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

// hostdomain/nodes diurutkan berdasarkan nama
function getNodes() {
    $sql = "SELECT * FROM nodes WHERE status='public' ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

function getNodeBy(){
    $node_id = Slim::getInstance()->request()->get('node_id');
    $username = Slim::getInstance()->request()->get('username');
    $status = Slim::getInstance()->request()->get('status');

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

    if (isset($node_id)){
        //$sql = "SELECT nodes.name as name, nodes.location as location, nodes.lat as lattitude, nodes.lon as longitude, nodes.date_time as created_date, nodes.user_id as owner, nodes.gateway_id as gateway, nodes.battery_id as battery, nodes.status as status, triggers.name as parameter, triggers.lower_limit as lower_limit, triggers.upper_limit as upper_limit from nodes JOIN triggers ON node_id=:id";
        $sql = "SELECT node_id, name, location, lat as latitude, lon as longitude, gateway_id, username, battery_id, wind_speed, wind_direction, par, rainfall, leaf_wetness, temperature, humidity, water_level, soil_temp, watertension from nodes WHERE node_id=:node_id";

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

    if (isset($node_id) && isset($wind_speed)){
        $sql = "SELECT wind_speed from nodes WHERE node_id=:node_id";
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

    if (isset($username) && isset($status)){
        $sql = "SELECT * from nodes WHERE username=:username AND status=:status";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("username", $username);
            $stmt->bindParam("status", $status);
            $stmt->execute();
            $node = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($node);
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}

function getPrivateNodeBy(){
    $user_id = Slim::getInstance()->request()->get('user_id');

    if (isset($user_id)){
        $sql = "SELECT * from nodes WHERE user_id=:user_id AND status='private'";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("user_id", $user_id);
            $stmt->execute();
            $node = $stmt->fetchAll(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($node);
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }
}

//hostdomain/nodes/:name
function getNode($name) {
    $sql = "SELECT * FROM nodes WHERE name=:name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("name", $name);
        $stmt->execute();
        $node = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($node);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

// function getSensorWindSpeed($node_id) {
//     $sql = "SELECT n.wind_speed as wind_speed, t.lower_limit as lower_limit, t.upper_limit as upper_limit FROM nodes n, triggers t WHERE n.node_id=t.node_id=:node_id and t.name = 'wind_speed'";
//     try {
//         $db = getConnection();
//         $stmt = $db->query($sql);
//         $stmt->bindParam("node_id", $node_id);
//         $stmt->execute();
//         $node = $stmt->fetchObject();
//         $db = null;
//         echo json_encode($node);
//     } catch(PDOException $e) {
//         echo '{"error":{"text":"'. $e->getMessage() .'"}}';
//     }
// }

// function getSensorWindDir($node_id) {
//     $sql = "SELECT n.wind_direction as wind_direction, t.lower_limit as lower_limit, t.upper_limit as upper_limit FROM nodes n, triggers t WHERE node_id=:node_id and t.name = 'wind_direction'";
//     try {
//         $db = getConnection();
//         $stmt = $db->prepare($sql);
//         $stmt->bindParam("node_id", $node_id);
//         $stmt->execute();
//         $node = $stmt->fetchObject();
//         $db = null;
//         echo json_encode($node);
//     } catch(PDOException $e) {
//         echo '{"error":{"text":"'. $e->getMessage() .'"}}';
//     }
// }



//hostdomain/nodes/public
function getPublicNodes() {
    $sql = "SELECT * FROM nodes WHERE status='public'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/private
function getPrivateNodes() {
    $sql = "SELECT * FROM nodes WHERE status='private'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/wind_speed
// melihat sensor wind_speed yang aktif di node mana aja
function getActWindSpeed() {
    $sql = "SELECT name, node_id, wind_speed FROM nodes WHERE wind_speed = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/wind_direction
function getActWindDirection() {
    $sql = "SELECT name, node_id, wind_direction FROM nodes WHERE wind_direction = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/par
function getActPAR() {
    $sql = "SELECT name, node_id, par FROM nodes WHERE par = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/rainfall
function getActRainfall() {
    $sql = "SELECT name, node_id, rainfall FROM nodes WHERE rainfall = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/leaf_wetness
function getActLeafWetness() {
    $sql = "SELECT name, node_id, leaf_wetness FROM nodes WHERE leaf_wetness = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/temperature
function getActTemperature() {
    $sql = "SELECT name, node_id, temperature FROM nodes WHERE temperature = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/humidity
function getActHumidity() {
    $sql = "SELECT name, node_id, humidity FROM nodes WHERE humidity = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/water_level
function getActWaterLevel() {
    $sql = "SELECT name, node_id, water_level FROM nodes WHERE water_level = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/soil_temp
function getActSoilTemp() {
    $sql = "SELECT name, node_id, soil_temp FROM nodes WHERE soil_temp = '1'";
    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/sensors/active/watertension
function getActMoisture() {
    $sql = "SELECT name, node_id, watertension FROM nodes WHERE watertension = '1'";

    try {
        $db = getConnection();
        $stmt = $db->query($sql);
        $nodes = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo '{"Nodes": ' . json_encode($nodes) . '}';
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/add/:id
function addNode() {
        $request = Slim::getInstance()->request();
        $node = json_decode($request->getBody());
        $username = $node->username;

        $sql = "INSERT INTO nodes (node_id, name, location, lat, lon, wind_speed, wind_direction, par, rainfall, leaf_wetness, temperature, humidity, water_level, soil_temp, watertension, username, gateway_id, battery_id, status) VALUES (:node_id, :name, :location, :lat, :lon, :wind_speed, :wind_direction, :par, :rainfall, :leaf_wetness, :temperature, :humidity, :water_level, :soil_temp, :watertension, (SELECT username FROM user WHERE username=:username), :gateway_id, :battery_id, :status) ";

        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node->node_id);
            $stmt->bindParam("name", $node->name);
            $stmt->bindParam("location", $node->location);
            $stmt->bindParam("lat", $node->lat);
            $stmt->bindParam("lon", $node->lon);
            $stmt->bindParam("wind_speed", $node->wind_speed);
            $stmt->bindParam("wind_direction", $node->wind_direction);
            $stmt->bindParam("par", $node->par);
            $stmt->bindParam("rainfall", $node->rainfall);
            $stmt->bindParam("leaf_wetness", $node->leaf_wetness);
            $stmt->bindParam("temperature", $node->temperature);
            $stmt->bindParam("humidity", $node->humidity);
            $stmt->bindParam("water_level", $node->water_level);
            $stmt->bindParam("soil_temp", $node->soil_temp);
            $stmt->bindParam("watertension", $node->watertension);
            $stmt->bindParam("username", $username);
            $stmt->bindParam("gateway_id", $node->gateway_id);
            $stmt->bindParam("battery_id", $node->battery_id);
            $stmt->bindParam("status", $node->status);
            $stmt->execute();
            $node->id = $db->lastInsertId();
            $db = null;
            echo "1";
            //echo json_encode($node);
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    //}
}

function updateNodeby(){
    $node_id = Slim::getInstance()->request()->get('node_id');
    $new_id = Slim::getInstance()->request()->get('new_id');
    $name = Slim::getInstance()->request()->get('name');
    $location = Slim::getInstance()->request()->get('location');
    $lat = Slim::getInstance()->request()->get('lat');
    $lon = Slim::getInstance()->request()->get('lon');
    $status = Slim::getInstance()->request()->get('status');
    $gateway_id = Slim::getInstance()->request()->get('gateway');
    $battery_id = Slim::getInstance()->request()->get('battery');
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

    if (isset($new_id) && isset($node_id)){
        $sql = "UPDATE nodes SET node_id=:new_id WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("new_id", $new_id);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($name) && isset($node_id)){
        $sql = "UPDATE nodes SET name=:name WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("name", $name);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($location) && isset($node_id)){
        $sql = "UPDATE nodes SET location=:location WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("location", $location);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($lat) && isset($node_id)){
        $sql = "UPDATE nodes SET lat=:lat WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("lat", $lat);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($lon) && isset($node_id)){
        $sql = "UPDATE nodes SET lon=:lon WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("lon", $lon);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($status) && isset($node_id)){
        $sql = "UPDATE nodes SET status=:status WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("status", $status);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($gateway_id) && isset($node_id)){
        $sql = "UPDATE nodes SET gateway_id=:gateway WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("gateway", $gateway_id);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($battery_id) && isset($node_id)){
        $sql = "UPDATE nodes SET battery_id=:battery WHERE node_id=:node_id";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("battery", $battery_id);
            $stmt->execute();
            $db = null;
            echo "1";
        } catch(PDOException $e) {
            echo '{"error":{"text":"'. $e->getMessage() .'"}}';
        }
    }

    if (isset($wind_speed) && isset($node_id)){
        $sql = "UPDATE nodes SET wind_speed=:wind_speed WHERE node_id=:node_id";
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

    if (isset($wind_direction) && isset($node_id)){
        $sql = "UPDATE nodes SET wind_direction=:wind_direction WHERE node_id=:node_id";
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

    if (isset($par) && isset($node_id)){
        $sql = "UPDATE nodes SET par=:par WHERE node_id=:node_id";
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

    if (isset($rainfall) && isset($node_id)){
        $sql = "UPDATE nodes SET rainfall=:rainfall WHERE node_id=:node_id";
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

    if (isset($leaf_wetness) && isset($node_id)){
        $sql = "UPDATE nodes SET leaf_wetness=:leaf_wetness WHERE node_id=:node_id";
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

    if (isset($temperature) && isset($node_id)){
        $sql = "UPDATE nodes SET temperature=:temperature WHERE node_id=:node_id";
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

    if (isset($humidity) && isset($node_id)){
        $sql = "UPDATE nodes SET humidity=:humidity WHERE node_id=:node_id";
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

    if (isset($water_level) && isset($node_id)){
        $sql = "UPDATE nodes SET water_level=:water_level WHERE node_id=:node_id";
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

    if (isset($soil_temp) && isset($node_id)){
        $sql = "UPDATE nodes SET soil_temp=:soil_temp WHERE node_id=:node_id";
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

    if (isset($watertension) && isset($node_id)){
        $sql = "UPDATE nodes SET watertension=:watertension WHERE node_id=:node_id";

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

//hostdomain/nodes/delete/:name
function deleteNode($node_id) {
    $sql = "DELETE FROM nodes WHERE node_id=:node_id";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("node_id", $node_id);
        $stmt->execute();
        $db = null;
        echo "1";
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}

//hostdomain/nodes/search/:query
function findNodeByName($query) {
    $sql = "SELECT * FROM nodes WHERE UPPER(name) LIKE :query AND status='public' ORDER BY name";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $query = "%".$query."%";
        $stmt->bindParam("query", $query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($users);
    } catch(PDOException $e) {
        echo '{"error":{"text":"'. $e->getMessage() .'"}}';
    }
}