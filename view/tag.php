<?php

global $db;

if($_REQUEST["tag"]=="create") {
    
    if(intval($_REQUEST['taggroup']) < 1 ) {
        echo json_encode(array("error"=>"Error: Tag Group is required.")); die;
    }

    if(!filter_var($_REQUEST["muid"], FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^((?=[ -~])[^:])+$/")))) {
        echo json_encode(array("error"=>"Error: MUID contains invalid characters or is empty.")); die;
    }

    if(strlen($_REQUEST['name_de']) < 1 ) {
        echo json_encode(array("error"=>"Error: Name (DE) is required.")); die;
    }


    // check if it is a numerical type
    $type = null;
    if(strlen($_REQUEST["type"])>0) {
        if(!in_array($_REQUEST["type"],unserialize(NUMERICAL_VALUE_TYPES))) {
            echo json_encode(array("error"=>"Error: Numerical value type is unknown.")); die;
        } else {

            if( strpos($_REQUEST["muid"],"$")===false || strpos($_REQUEST["name_de"],"$")===false) {

                echo json_encode(array("error"=>"Error: Numerical Placeholders missing.")); die;

                if( (strpos($_REQUEST["muid"],"~")===false || strpos($_REQUEST["name_de"],"~")===false) && $_REQUEST["type"]!="numeric") {
                    echo json_encode(array("error"=>"Error: Numerical Unit Placeholders missing.")); die;
                }

            } else {
                $type = $_REQUEST["type"];
            }


        }
    }


    $stmt = $db->prepare("INSERT INTO tag (taggroup,muid,name_de,name_at,type) VALUES (:taggroup,:muid,:name_de,:name_at,:type)");
    $stmt->bindValue(":taggroup",intval($_REQUEST["taggroup"]));
    $stmt->bindValue(":muid",ucfirst($_REQUEST["muid"]));
    $stmt->bindValue(":name_de",ucfirst($_REQUEST["name_de"]));
    $stmt->bindValue(":name_at",ucfirst(strval($_REQUEST["name_at"])));
    $stmt->bindValue(":type",$type);

    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".json_encode($db->errorInfo()[0]))); die;
    } else {

        $stmt = $db->prepare("SELECT * from tag WHERE muid = :muid");
        $stmt->bindValue(":muid",ucfirst($_REQUEST["muid"]));
        $stmt->execute();

        echo json_encode($stmt->fetch());
    }

} else if($_REQUEST["tag"]=="delete") {

    // there should be an sql restrict on connections with tags which are in use
    $stmt = $db->prepare("DELETE FROM tag WHERE id=:id AND gs1_attribute_value_code IS NULL");
    $stmt->bindValue(":id",$_REQUEST["id"]);
    $stmt->execute();

    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
    } else {
        echo "success"; die;
    }

    echo json_encode(array("error"=>"Tag deletion is not allowed. The Object is connected to some article.")); die;
}