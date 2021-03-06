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
} else if($_REQUEST["ingredient"] == "update" || $_REQUEST["ingredient"] == "update_name") {
    if ($_REQUEST["ingredient"] == "update") {
        $stmt = null;
        $ingredId = $_GET["ingredient_id"];

        // should only be one
        foreach($_POST as $key => $value) {
            $stmt = $db->prepare("UPDATE ingredient SET ".$key." = :value WHERE id = :id");
            $stmt->bindValue(":value",$value,PDO::PARAM_BOOL);
            $stmt->bindValue(":id", $ingredId,PDO::PARAM_INT);
        }
    } else {
        $ingredId = $_POST["id"];
        $stmt = $db->prepare("UPDATE ingredient SET name=:name WHERE id = :id");
        $stmt->bindValue(":name",$_POST["name"]);
        $stmt->bindValue(":id",$ingredId,PDO::PARAM_INT);
    }
    
    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
    } else {
        
        $stmt = $db->prepare("SELECT * from ingredient WHERE id = :id");
        $stmt->bindValue(":id",$ingredId);
        $stmt->execute();
        
        echo json_encode($stmt->fetch());
    }
} else if($_REQUEST["ingredient"]=="delete") {
    
    // if
    $stmt = $db->prepare("SELECT * from fdata_ingredient WHERE ingredient_id=:ingredient_id");
    $stmt->bindValue(":ingredient_id",$_REQUEST["ingredient_id"]);
    $stmt->execute();
    
    $con = $stmt->fetchAll();
    
    if(count($con) < 2) {
        $row = $con[0];
        
        // deletion allowed if the ingredient is only connected to the current product
        if($row["fdata_id"]==$_REQUEST["fdata_id"] || count($con) === 0) {
            $stmt = $db->prepare("DELETE FROM ingredient WHERE id=:ingredient_id");
            $stmt->bindValue(":ingredient_id",$_REQUEST["ingredient_id"]);
            $stmt->execute();
                
            if(!$stmt->execute()) {
                echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
            } else {
                echo "success"; die;
            }
        }
    }
    echo json_encode(array("error"=>"Ingredient deletion is not allowed.")); die;
}