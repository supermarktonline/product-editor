<?php


global $db;

if(!isset($_REQUEST["category_id"]) || intval($_REQUEST["category_id"])<1 ) {
    echo "Missing category id."; die;
    die;
}


if(isset($_REQUEST["category_sealetc_connection"]) && $_REQUEST["category_sealetc_connection"]=="update") {

    // 1. Delete the old connections
    $stmt = $db->prepare("DELETE FROM category_sealetc WHERE category_id = :category_id");
    $stmt->bindValue(":category_id",$_REQUEST["category_id"]);

    if(!$stmt->execute()) {
        echo "Error recreating category/seal connections."; die;
    } else {

        $stmt = $db->prepare("INSERT INTO category_sealetc (category_id,sealetc_id) VALUES (:category_id,:sealetc_id)");

        foreach($_REQUEST["ids"] as $id) {

            $stmt->bindValue(":category_id",$_REQUEST["category_id"]);
            $stmt->bindValue(":sealetc_id",intval($id));

            if(!$stmt->execute()) {
                echo "SQL Failure: ".$db->errorInfo()[2]." ";
            }
        }

        echo "success"; die;

    }
} else if(isset($_REQUEST["category_sealetc_connection"]) && $_REQUEST["category_sealetc_connection"]=="get") {
    
    // get the connections of this category
    $direct = array();
    
    $stmt = $db->prepare("SELECT sealetc_id FROM category_sealetc WHERE category_id = :category_id");
    $stmt->bindValue(":category_id",$_REQUEST["category_id"]);
    
    if(!$stmt->execute()) {
        echo "SQL Failure: ".$db->errorInfo()[2]."."; die;
    } else {
        $direct = $stmt->fetchAll();
    }
    
    // get the connections of the parent category
    $parent_ids = json_decode($_REQUEST["parent_ids"]);
    
    foreach($parent_ids as $key => $parid) {
        $parent_ids[$key] = intval($parid);
    }
    
    $parent = array();
    
    if(!empty($parent_ids)) {
    
        // not nice but secure enough
        $stmt2 = $db->prepare("SELECT sealetc_id FROM category_sealetc WHERE category_id IN (".implode(",",$parent_ids).")");

        if(!$stmt2->execute()) {
            echo "SQL Failure: ".$db->errorInfo()[2]."."; die;
        } else {
            $parent = $stmt2->fetchAll();
        }
    }
    
    echo json_encode(array(
        "direct" => $direct,
        "parent" => $parent
        ));
    die;
}

echo "Please specifiy an action."; die;