<?php

global $db;


if($_GET["ingredient_connection"]=="get") {
    
    $stmt = $db->prepare("SELECT * FROM fdata_ingredient WHERE fdata_id= :fdata_id");
    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
    $stmt->execute();
    echo json_encode($stmt->fetchAll()); die;
    
} else {

    $dbstr = "";

    if($_GET["ingredient_connection"]=="create") {
        $dbstr = "INSERT INTO fdata_ingredient (fdata_id,ingredient_id) VALUES (:fdata_id,:ingredient_id)";
    } else if($_GET["ingredient_connection"]=="delete") {
        $dbstr = "DELETE FROM fdata_ingredient WHERE fdata_id = :fdata_id AND ingredient_id = :ingredient_id";
    } else {
        echo "API Failure"; die;
    }

    $stmt = $db->prepare($dbstr);

    $stmt->bindValue(":fdata_id",$_REQUEST["fdata_id"]);
    $stmt->bindValue(":ingredient_id",$_REQUEST["ingredient_id"]);

    if(!$stmt->execute()) {
        echo "SQL Failure: ".$db->errorInfo()[2]; die;
    }

    echo "success"; die;
}