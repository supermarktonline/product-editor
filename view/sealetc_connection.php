<?php


global $db;

if(!isset($_REQUEST["fdata_id"]) || intval($_REQUEST["fdata_id"])<1 ) {
    echo "Missing product id"; die;
    die;
}


if(isset($_REQUEST["sealetc_connection"]) && $_REQUEST["sealetc_connection"]=="update") {

    // 1. Delete the old connections
    $stmt = $db->prepare("DELETE FROM fdata_sealetc WHERE fdata_id = :fdata_id");
    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);

    if(!$stmt->execute()) {
        echo "Error recreating seal connections."; die;
    } else {

        $stmt = $db->prepare("INSERT INTO fdata_sealetc (fdata_id,sealetc_id) VALUES (:fdata_id,:sealetc_id)");

        foreach($_REQUEST["ids"] as $id) {

            $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
            $stmt->bindValue(":sealetc_id",intval($id));

            if(!$stmt->execute()) {
                echo "SQL Failure: ".$db->errorInfo()[2]." ";
            }
        }

        echo "success"; die;

    }
} else if(isset($_REQUEST["sealetc_connection"]) && $_REQUEST["sealetc_connection"]=="get") {
    
    $stmt = $db->prepare("SELECT sealetc_id FROM fdata_sealetc WHERE fdata_id = :fdata_id");
    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
    
    if(!$stmt->execute()) {
        echo "SQL Failure: ".$db->errorInfo()[2]."."; die;
    } else {
        
        echo json_encode($stmt->fetchAll()); die;
    }
    
}

echo "Please specifiy an action."; die;