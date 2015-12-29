<?php

global $db;

if($_REQUEST["sealetc"]=="create") {
    
    if(intval($_REQUEST['taggroup']) < 1 ) {
        echo json_encode(array("error"=>"Error: Tag Group is required.")); die;
    }

    if(!filter_var($_REQUEST["muid"], FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^((?=[ -~])[^:])+$/")))) {
        echo json_encode(array("error"=>"Error: MUID contains invalid characters or is empty.")); die;
    }

    if(strlen($_REQUEST['name_de']) < 1 ) {
        echo json_encode(array("error"=>"Error: Name (DE) is required.")); die;
    }

    $numerical_value = null;
    $nv_prep =str_replace(",",".",trim($_REQUEST['numerical_value']));

    if(strlen($nv_prep)>0) {
        if(!is_numeric($nv_prep)) {
            echo json_encode(array("error"=>"Error: Numerical value must be numeric or empty.")); die;
        } else {
            $numerical_value = floatval($nv_prep);
        }
    }

    $numerical_value_type = null;

    if(!is_null($numerical_value)) {
        if(!in_array($_REQUEST["numerical_value_type"],unserialize(NUMERICAL_VALUE_TYPES))) {
            echo json_encode(array("error"=>"Error: Numerical value type is unknown.")); die;
        } else {
            $numerical_value_type = $_REQUEST["numerical_value_type"];
        }
    }
    
    $stmt = $db->prepare("INSERT INTO sealetc (taggroup,muid,name_de,name_at,numerical_value,numerical_value_type) VALUES (:taggroup,:muid,:name_de,:name_at,:numerical_value,:numerical_value_type)");
    $stmt->bindValue(":taggroup",intval($_REQUEST["taggroup"]));
    $stmt->bindValue(":muid",ucfirst($_REQUEST["muid"]));
    $stmt->bindValue(":name_de",ucfirst($_REQUEST["name_de"]));
    $stmt->bindValue(":name_at",ucfirst(strval($_REQUEST["name_at"])));
    $stmt->bindValue(":numerical_value",$numerical_value);
    $stmt->bindValue(":numerical_value_type",$numerical_value_type);


    
    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".json_encode($db->errorInfo()[0]))); die;
    } else {
        
        $stmt = $db->prepare("SELECT * from sealetc WHERE muid = :muid");
        $stmt->bindValue(":muid",ucfirst($_REQUEST["muid"]));
        $stmt->execute();
        
        echo json_encode($stmt->fetch());
    }



// TODO DELETION
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