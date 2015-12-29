<?php


global $db;

if(!isset($_REQUEST["fdata_id"]) || intval($_REQUEST["fdata_id"])<1 ) {
    echo "Missing product id"; die;
    die;
}


if(isset($_REQUEST["tag_connection"]) && $_REQUEST["tag_connection"]=="update") {

    // 1. Delete the old connections
    $stmt = $db->prepare("DELETE FROM fdata_tag WHERE fdata_id = :fdata_id");
    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);

    if(!$stmt->execute()) {
        echo "Error recreating tag connections."; die;
    } else {

        $stmt = $db->prepare("INSERT INTO fdata_tag (fdata_id,tag_id) VALUES (:fdata_id,:tag_id)");

        foreach($_REQUEST["ids"] as $id) {

            $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
            $stmt->bindValue(":tag_id",intval($id));

            if(!$stmt->execute()) {
                echo "SQL Failure: ".$db->errorInfo()[2]." ";
            }
        }

        echo "success"; die;

    }
} else if(isset($_REQUEST["tag_connection"]) && $_REQUEST["tag_connection"]=="get") {
    
    $stmt = $db->prepare("SELECT tag_id FROM fdata_tag WHERE fdata_id = :fdata_id");
    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
    
    if(!$stmt->execute()) {
        echo "SQL Failure: ".$db->errorInfo()[2]."."; die;
    } else {
        
        echo json_encode($stmt->fetchAll()); die;
    }
    
}

echo "Please specifiy an action."; die;