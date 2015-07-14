<?php

global $db;

if($_REQUEST["sealetc"]=="create") {
    
    if(strlen($_REQUEST["name"]) < 1 ) {
        echo json_encode(array("error"=>"Error: Seal Name must not be empty.")); die;
    }
    
    $stmt = $db->prepare("INSERT INTO sealetc (name) VALUES (:name)");
    $stmt->bindValue(":name",ucfirst($_REQUEST["name"]));
    
    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
    } else {
        
        $stmt = $db->prepare("SELECT * from sealetc WHERE name = :name");
        $stmt->bindValue(":name",ucfirst($_REQUEST["name"]));
        $stmt->execute();
        
        echo json_encode($stmt->fetch());
    }
    
} else if($_REQUEST["sealetc"]=="delete") {
    
    
    $stmt = $db->prepare("SELECT * from fdata_sealetc con, sealetc AS m WHERE con.sealetc_id = m.id and m.name=:name");
    $stmt->bindValue(":name",$_REQUEST["name"]);
    $stmt->execute();
    
    $con = $stmt->fetchAll();
        
    // deletion allowed if the seal is not connected to any product
    if(count($con) === 0) {
        $stmt = $db->prepare("DELETE FROM sealetc WHERE name=:name");
        $stmt->bindValue(":name",$_REQUEST["name"]);
        $stmt->execute();

        if(!$stmt->execute()) {
            echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
        } else {
            echo "success"; die;
        }
    }
    echo json_encode(array("error"=>"Seal (etc.) deletion is not allowed. The Object is connected to some article.")); die;
}