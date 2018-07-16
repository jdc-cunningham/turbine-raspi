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
		
            $ohm = 31.6; // my particular fixed load some resistors in parallel
            $multiplier = 5/1024; // reference voltage vs. adc steps
            $first_five = [];
            $total_watts = 0;
            $output_counter = 0;

            foreach ($val_arr as $va_val) {
                if ($output_counter < 5) {
                    array_push($first_five, $va_val);
                }
                $voltage = round($va_val * $multiplier, 3);
                $current = round($voltage / $ohm, 4);
                $total_watts += $voltage * $current;
                $output_counter++;
            }
            
            echo round($total_watts*100000, 3) .' micro watts<br>';
            
	    $voltage = round($first_five[0] * $multiplier, 3);
	    $current = round($voltage / $ohm, 4);
	    echo 'Highest: ' . $voltage . 'V ' . $current . 'A<br>';
        } 
        else  {
	        echo "tis empty my boy";
	    }
    }
