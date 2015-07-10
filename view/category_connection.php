<?php


global $db;

if(!isset($_REQUEST["fdata_id"]) || intval($_REQUEST["fdata_id"])<1 ) {
    echo "Missing product id"; die;
    die;
}


if(isset($_REQUEST["category_connection"]) && $_REQUEST["category_connection"]=="update") {

    // 1. Delete the old connections
    $stmt = $db->prepare("DELETE FROM fdata_category WHERE fdata_id = :fdata_id");
    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);

    if(!$stmt->execute()) {
        echo "Error recreating category connections."; die;
    } else {

        $stmt = $db->prepare("INSERT INTO fdata_category (fdata_id,category_id) VALUES (:fdata_id,:category_id)");

        foreach($_REQUEST["categories"] as $c) {

            $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
            $stmt->bindValue(":category_id",intval($c));

            if(!$stmt->execute()) {
                echo "SQL Failure: ".$db->errorInfo()[2]." ";
            }

        }

        echo "success"; die;

    }
} else if(isset($_REQUEST["category_connection"]) && $_REQUEST["category_connection"]=="get") {
    
    $stmt = $db->prepare("SELECT category_id FROM fdata_category WHERE fdata_id = :fdata_id");
    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
    
    if(!$stmt->execute()) {
        echo "SQL Failure: ".$db->errorInfo()[2]."."; die;
    } else {
        
        echo json_encode($stmt->fetchAll()); die;
    }
    
}

echo "Please specifiy an action."; die;