<?php

global $db;

if($_REQUEST["ingredient"]=="create") {
    
    $stmt = $db->prepare("INSERT INTO ingredient (name) VALUES (:name)");
    $stmt->bindValue(":name",ucfirst($_REQUEST["name"]));
    
    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
    } else {
        
        $stmt = $db->prepare("SELECT * from ingredient WHERE name = :name");
        $stmt->bindValue(":name",ucfirst($_REQUEST["name"]));
        $stmt->execute();
        
        echo json_encode($stmt->fetch());
    }
} else if($_REQUEST["ingredient"]=="update") {
    
    $stmt = null;
    
    // should only be one
    foreach($_POST as $key => $value) {
        $stmt = $db->prepare("UPDATE ingredient SET ".$key." = :value WHERE id = :id");
        $stmt->bindValue(":value",$value,PDO::PARAM_BOOL);
        $stmt->bindValue(":id",$_GET["ingredient_id"],PDO::PARAM_INT);
    }
    
    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
    } else {
        
        $stmt = $db->prepare("SELECT * from ingredient WHERE id = :id");
        $stmt->bindValue(":id",$_GET["ingredient_id"]);
        $stmt->execute();
        
        echo json_encode($stmt->fetch());
    }
    
}