<?php
/**
 * Updating Products.
 */

global $db;

if(!isset($_POST["id"]) || intval($_POST["id"])<1 || intval($_POST["id"])>PHP_INT_MAX ) {
    die;
}

// replace whitespace for column with whitepace in it
$wrep = function($key) {
    return str_replace("___"," ",$key);
};

$update_query = 'UPDATE fdata SET ';

$first = false;
foreach($_POST AS $key => $value) {
    if($key!=="id") {
        $update_query .= ($first) ? ",":"";
        $update_query .= '"'.$wrep($key).'" = :'.$key;
        $first = true;
    }
}

$update_query .= " WHERE id = :id";

$stmt = $db->prepare($update_query);


foreach($_POST AS $key => $value) {
    $stmt->bindValue(":".$key,$value);
}

if(!$stmt->execute()) {
    echo "SQL Failure: ".$db->errorInfo()[2]; die;
}

echo "success"; die;