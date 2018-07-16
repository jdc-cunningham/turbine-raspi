<?php

    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'db-connect.php');

    $stmt = $dbh->prepare('SELECT a_val FROM turbine_data WHERE a_val > 0 AND date_time >= CURDATE()');
    if ($stmt->execute()) {
        $result = $stmt->fetchAll();
        if (!empty($result)) {
            $val_arr = [];
                foreach ($result as $row) {
                    $a_val = $row['a_val'];
                    array_push($val_arr, $a_val);
            }
            // sort
            rsort($val_arr);
            echo 'max: ';
            foreach ($val_arr as $va_val) {
                $ohm = 31.6;
                $multiplier = 5/1024;
                $voltage = round($va_val * $multiplier, 3);
                $current = round($voltage / $ohm, 4);
                echo $voltage . 'V ' . $current . 'A<br>';
            }
        } 
        else  {
	        echo "tis empty my boy";
	    }
    }
