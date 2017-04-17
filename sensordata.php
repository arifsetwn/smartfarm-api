<?php
header("Access-Control-Allow-Origin: *");

use Slim\Slim;

function sendNotification($title, $content) {
    // API access key from Google API's Console
    define( 'API_ACCESS_KEY', 'AAAAEmxTJ3Q:APA91bGWY5M_yfWsUkCe0Tw3ZMMnH6SxT_0bkFMQCKxHv8GEzStMldBntwyiCa_VKJie0n1Fq_rw85-eD5cvDT3sLkunbLYruGzNbZuojsWiNKXEHGh-Tg5cDa0yutUvc4M4AV38jYhw' );

    // Send through topic
    $fields = array(
        'to'    => '/topics/alert',
        'data'  => array(
            'Extra value1'  => 'Foo',
            'Extra value2'  => 'Bar'
        ),

        'notification'  => array(
            "title"        => $title,  //Any value
            "body"         => $content,  //Any value
            "color"        => "#666666",
            "sound"        => "default", //If you want notification sound
            "click_action" => "https://sawitfarm.com/log",  // Must be present for Android
            "icon"         => "fcm_push_icon"  // White icon Android resource
         )
    );

    $headers = array(
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );
    // echo $result . PHP_EOL;
}



function registerUserToken() {
    // API access key from Google API's Console
    define( 'API_ACCESS_KEY', 'AAAAEmxTJ3Q:APA91bGWY5M_yfWsUkCe0Tw3ZMMnH6SxT_0bkFMQCKxHv8GEzStMldBntwyiCa_VKJie0n1Fq_rw85-eD5cvDT3sLkunbLYruGzNbZuojsWiNKXEHGh-Tg5cDa0yutUvc4M4AV38jYhw' );

    $user = Slim::getInstance()->request()->params('user');
    $token = Slim::getInstance()->request()->params('token');

    $sql = 'INSERT INTO notification_token (id, user, firebase_token) VALUES (1, :user, :token) ON DUPLICATE KEY UPDATE firebase_token=:token';
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("user", $user);
        $stmt->bindParam("token", $token);
        $stmt->execute();
    } catch(\Exception $e) {
        print $e->getMessage();
        exit();
    }

    $headers = array(
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json',
        'Content-Length: 0',
    );

    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://iid.googleapis.com/iid/v1/' . $token . '/rel/topics/alert' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    $result = curl_exec($ch );
    curl_close( $ch );
    echo 'IID Google: ' . $result;
}

function GetSensorsData() 
{
    $node_id = Slim::getInstance()->request()->params('id');
    $te = Slim::getInstance()->request()->params('te');
    $hu = Slim::getInstance()->request()->params('hu');
    $pc = Slim::getInstance()->request()->params('pc');
    $ph = Slim::getInstance()->request()->params('ph');
    $pd = Slim::getInstance()->request()->params('pd');
    $an = Slim::getInstance()->request()->params('an');
    $wv = Slim::getInstance()->request()->params('wv');
    $bt = Slim::getInstance()->request()->params('bt');
    $st = Slim::getInstance()->request()->params('st');
    $sm = Slim::getInstance()->request()->params('sm');
    $lw = Slim::getInstance()->request()->params('lw');
    $sr = Slim::getInstance()->request()->params('sr');
    $wl = Slim::getInstance()->request()->params('wl');
    $pr = Slim::getInstance()->request()->params('pr');

    $trial = Slim::getInstance()->request()->params('trial');

    // Notify pusher about new data
    $options = array(
        'cluster' => 'ap1',
        'encrypted' => true
    );
    $pusher = new Pusher(
        '92a60b1a643d7b8e8a87',
        'bca59ce388dc8aa98bb4',
        '303934',
        $options
    );

    $data = [
        'node_id'      => $node_id,
        'temperature'  => $te,
        'humidity'     => $hu,
        'soil_temp'    => $st,
        'watertension' => $sm,
        'water_level'  => $wl,
        'wind_speed'   => $pc,
        'pl_current'   => $wv,
        'battery'      => $bt,
        'leaf_wetness' => $lw,
        'par'          => $sr,
        'wind_direction' => $wv,
    ];

    foreach ($data as $key => $val) {
        if (!isset($val)) {
            // Cleaning null data
            unset($data[$key]);
        } else if (!in_array($key, ['wind_direction', 'node_id'])) {
            $data[$key] = floatval($val);
        }
    }

    $pusher->trigger('data-bus', 'new-data', $data);

    if (isset($trial)) {
        return "TRIAL ONLY";
    }

    if (isset($te)) {
        $sql = "INSERT INTO temperature (node_id, temperature) VALUES (:node_id, :temperature)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("temperature", $te);
            $stmt->execute();
            $id = $db->lastInsertId();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'temperature' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];

            if ($te < $lower) {
                try {
                    $n_name = "SELECT name FROM nodes WHERE node_id = '$node_id'";
                    $stmt1 = $db->query($n_name);
                    $name = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $value = json_encode($name);

                    $message = 'Temperature is under threshold';

                    // call notification
                    sendNotification('Node ' . $value, $message);
                    // sendNotification($message . ' on node ' . $value);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('temperature', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $te);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($te > $upper) {
                try {
                    $message = 'Temperature is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('temperature', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $te);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }
            catch(PDOException $e) {
            echo '{"error1":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($hu)) {
        $sql = "INSERT INTO humidity (node_id, humidity) VALUES (:node_id, :humidity)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("humidity", $hu);
            $stmt->execute();
            $id = $db->lastInsertId();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'humidity' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($hu < $lower) {
                try {
                    $message = 'Humidity is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('humidity', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $hu);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($hu > $upper) {
                try {
                    $message = 'Humidity is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('humidity', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $hu);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error2":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($pc)) {
        $sql = "INSERT INTO pl_current (node_id, pl_current) VALUES (:node_id, :pl_current)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("pl_current", $pc);
            $stmt->execute();
            $id = $db->lastInsertId();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'pl_current' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($pc < $lower) {
                try {
                    $message = 'Rainfall is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('pl_current', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $pc);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($pc > $upper) {
                try {
                    $message = 'Rainfall is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('pl_current', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $pc);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error3":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($ph)) {
        $sql = "INSERT INTO pl_hour (node_id, pl_hour) VALUES (:node_id, :pl_hour)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("pl_hour", $ph);
            $stmt->execute();
            $id = $db->lastInsertId();

            // $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'pl_hour' AND node_id = '$node_id'";
            // $stmt1 = $db->query($threshold);
            // $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            // $lower = $nodes['lower_limit'];
            // $upper = $nodes['upper_limit'];
            // if ($ph < $lower) {
            //     try {
            //         $message = 'pl_hour is under threshold';

            //         // call notification
            //         sendNotification('Node ' . $node_id, $message);

            //         $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('pl_hour', :node_id, :value, :message)";
            //         $runalert = $db->prepare($alert);
            //         $runalert->bindParam("node_id", $node_id);
            //         $runalert->bindParam("value", $ph);
            //         $runalert->bindParam("message", $message);
            //         $runalert->execute();
            //         $id = $db->lastInsertId();
            //     }
            //         catch(PDOException $e) {
            //         echo '{"error1":{"text":'. $e->getMessage() .'}}';
            //     }
            // }
            // else if ($ph > $upper) {
            //     try {
            //         $message = 'pl_hour is above threshold';

            //         // call notification
            //         sendNotification('Node ' . $node_id, $message);

            //         $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('pl_hour', :node_id, :value, :message)";
            //         $runalert = $db->prepare($alert);
            //         $runalert->bindParam("node_id", $node_id);
            //         $runalert->bindParam("value", $ph);
            //         $runalert->bindParam("message", $message);
            //         $runalert->execute();
            //         $id = $db->lastInsertId();
            //     }
            //         catch(PDOException $e) {
            //         echo '{"error1":{"text":'. $e->getMessage() .'}}';
            //     }
            // }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error3":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($pd)) {
        $sql = "INSERT INTO pl_day (node_id, pl_day) VALUES (:node_id, :pl_day)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("pl_day", $pd);
            $stmt->execute();
            $id = $db->lastInsertId();

            // $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'pl_day' AND node_id = '$node_id'";
            // $stmt1 = $db->query($threshold);
            // $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            // $lower = $nodes['lower_limit'];
            // $upper = $nodes['upper_limit'];
            // if ($pd < $lower) {
            //     try {
            //         $message = 'pl_day is under threshold';

            //         // call notification
            //         sendNotification('Node ' . $node_id, $message);

            //         $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('pl_day', :node_id, :value, :message)";
            //         $runalert = $db->prepare($alert);
            //         $runalert->bindParam("node_id", $node_id);
            //         $runalert->bindParam("value", $pd);
            //         $runalert->bindParam("message", $message);
            //         $runalert->execute();
            //         $id = $db->lastInsertId();
            //     }
            //         catch(PDOException $e) {
            //         echo '{"error1":{"text":'. $e->getMessage() .'}}';
            //     }
            // }
            // else if ($pd > $upper) {
            //     try {
            //         $message = 'pl_day is above threshold';

            //         // call notification
            //         sendNotification('Node ' . $node_id, $message);

            //         $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('pl_day', :node_id, :value, :message)";
            //         $runalert = $db->prepare($alert);
            //         $runalert->bindParam("node_id", $node_id);
            //         $runalert->bindParam("value", $pd);
            //         $runalert->bindParam("message", $message);
            //         $runalert->execute();
            //         $id = $db->lastInsertId();
            //     }
            //         catch(PDOException $e) {
            //         echo '{"error1":{"text":'. $e->getMessage() .'}}';
            //     }
            // }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error3":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($an)) {
        $sql = "INSERT INTO wind_speed (node_id, wind_speed) VALUES (:node_id, :wind_speed)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("wind_speed", $an);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'windspeed' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($an < $lower) {
                try {
                    $message = 'Wind speed is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('windspeed', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $an);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($an > $upper) {
                try {
                    $message = 'Wind speed is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('windspeed', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $an);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error4":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($wv)) {
        $sql = "INSERT INTO wind_direction (node_id, wind_direction) VALUES (:node_id, :wind_direction)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("wind_direction", $wv);
            $id = $db->lastInsertId();
            $stmt->execute();
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error5":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($bt)) {
        $sql = "INSERT INTO battery (node_id, battery) VALUES (:node_id, :battery)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("battery", $bt);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'battery' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($bt < $lower) {
                try {
                    $message = 'Battery is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('battery', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $bt);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            // else if ($bt > $upper) {
            //     try {
            //         $message = 'Battery is above threshold';

            //         // call notification
            //         //sendNotification('Node ' . $node_id, $message);

            //         $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('battery', :node_id, :value, :message)";
            //         $runalert = $db->prepare($alert);
            //         $runalert->bindParam("node_id", $node_id);
            //         $runalert->bindParam("value", $bt);
            //         $runalert->bindParam("message", $message);
            //         $runalert->execute();
            //         $id = $db->lastInsertId();
            //     }
            //         catch(PDOException $e) {
            //         echo '{"error1":{"text":'. $e->getMessage() .'}}';
            //     }
            // }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error6":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($st)) {
        $sql = "INSERT INTO soil_temp (node_id, soil_temp) VALUES (:node_id, :soil_temp)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("soil_temp", $st);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'soiltemp' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($st < $lower) {
                try {
                    $message = 'Soil temperature is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('soiltemp', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $st);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($st > $upper) {
                try {
                    $message = 'Soil temperature is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('soiltemp', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $st);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error7":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($sm)) {
        $sql = "INSERT INTO watertension (node_id, watertension) VALUES (:node_id, :watertension)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("watertension", $sm);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'watertension' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($sm < $lower) {
                try {
                    $message = 'Water tension is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('watertension', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $sm);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($sm > $upper) {
                try {
                    $message = 'Water tension is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('watertension', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $sm);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error8":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($lw)) {
        $sql = "INSERT INTO leaf_wetness (node_id, leaf_wetness) VALUES (:node_id, :leaf_wetness)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("leaf_wetness", $lw);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'leafwetness' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($lw < $lower) {
                try {
                    $message = 'Leaf wetness is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('leafwetness', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $lw);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($lw > $upper) {
                try {
                    $message = 'Leaf wetness is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('leafwetness', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $lw);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error9":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($sr)) {
        $sql = "INSERT INTO par (node_id, par) VALUES (:node_id, :par)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("par", $sr);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'par' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($sr < $lower) {
                try {
                    $message = 'PAR is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('par', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $sr);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($sr > $upper) {
                try {
                    $message = 'PAR is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('par', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $sr);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error10":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($wl)) {
        $sql = "INSERT INTO water_level (node_id, water_level) VALUES (:node_id, :water_level)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("water_level", $wl);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'waterlevel' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($wl < $lower) {
                try {
                    $message = 'Water level is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('waterlevel', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $wl);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($wl > $upper) {
                try {
                    $message = 'Water level is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('waterlevel', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $wl);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error11":{"text":'. $e->getMessage() .'}}';
        }
    }

    if (isset($pr)) {
        $sql = "INSERT INTO air_pressure (node_id, air_pressure) VALUES (:node_id, :air_pressure)";
        try {
            $db = getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindParam("node_id", $node_id);
            $stmt->bindParam("air_pressure", $pr);
            $id = $db->lastInsertId();
            $stmt->execute();

            $threshold = "SELECT lower_limit, upper_limit FROM threshold WHERE name = 'air_pressure' AND node_id = '$node_id'";
            $stmt1 = $db->query($threshold);
            $nodes = $stmt1->fetch(PDO::FETCH_ASSOC);;
            $lower = $nodes['lower_limit'];
            $upper = $nodes['upper_limit'];
            if ($pr < $lower) {
                try {
                    $message = 'Air pressure is under threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('air_pressure', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $pr);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            else if ($pr > $upper) {
                try {
                    $message = 'Air pressure is above threshold';

                    // call notification
                    //sendNotification('Node ' . $node_id, $message);

                    $alert = "INSERT INTO alert (sensor_name, node_id, value, status) VALUES ('air_pressure', :node_id, :value, :message)";
                    $runalert = $db->prepare($alert);
                    $runalert->bindParam("node_id", $node_id);
                    $runalert->bindParam("value", $pr);
                    $runalert->bindParam("message", $message);
                    $runalert->execute();
                    $id = $db->lastInsertId();
                }
                    catch(PDOException $e) {
                    echo '{"error1":{"text":'. $e->getMessage() .'}}';
                }
            }
            $db = null;
            echo "1";
        }   catch(PDOException $e) {
            echo '{"error11":{"text":'. $e->getMessage() .'}}';
        }
    }
}
