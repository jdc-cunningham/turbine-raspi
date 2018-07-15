<?php

    // continue
    require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR.'db-connect.php');

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        // set date
        date_default_timezone_set('America/Chicago');

        // check for api key
        $post_key = $_GET['post_key'];

        if ($post_key !== '') { // put a long random string here that matches Raspberry Pi's side in thread-plot.py
            echo json_encode($return['status'] = 'invalid key');
            exit;
        }

        // get variables
        $analog_vals = $_GET['t_val_pairs'];
        $date_time = date('Y-m-d H:i:s');

        // loop through pairs
        if (strpos($analog_vals, ';') !== false) {
            $str_parts = explode(';', $analog_vals);
    
            foreach ($str_parts as $str_val) {
                if (empty($str_val)) {
                    continue; // skip
                }
                $str_val_parts = explode(',', $str_val);
                $time_stamp = $str_val_parts[0];
                $analog_val = $str_val_parts[1];
                $stmt = $dbh->prepare('INSERT INTO turbine_data VALUES (:id, :a_val, :time_stamp, :date_time)');
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':a_val', $analog_val, PDO::PARAM_INT);
                $stmt->bindParam(':time_stamp', $time_stamp, PDO::PARAM_INT);
                $stmt->bindParam(':date_time', $date_time, PDO::PARAM_STR);
                if ($stmt->execute()) {
                    $status = 'post success';
                }
                else {
                    $status = 'post fail';
                }
            }
        }
        else if (!empty($analog_vals) && strpos($analog_vals, ',') !== false) {
            $str_parts = explode(',', $analog_vals);
            $time_stamp = $str_parts[0];
            $analog_val = $str_parts[1];
            $stmt = $dbh->prepare('INSERT INTO turbine_data VALUES (:id, :a_val, :time_stamp, :date_time)');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':a_val', $analog_val, PDO::PARAM_INT);
            $stmt->bindParam(':time_stamp', $time_stamp, PDO::PARAM_INT);
            $stmt->bindParam(':date_time', $date_time, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $status = 'post success';
            }
            else {
                $status = 'post fail';
            }
        }
        echo json_encode($return['status'] = $status);
        exit;
    }
