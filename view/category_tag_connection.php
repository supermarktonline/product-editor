<?php


global $db;

if(!isset($_REQUEST["category_id"]) || intval($_REQUEST["category_id"])<1 ) {
    echo "Missing category id."; die;
    die;
}


if(isset($_REQUEST["category_tag_connection"]) && $_REQUEST["category_tag_connection"]=="update") {

    // 1. Delete old connections of non-gs1 tags
    $stmt = $db->prepare("DELETE FROM category_tag WHERE category_id = :category_id AND tag_id IN (SELECT id FROM tag WHERE gs1_attribute_value_code IS NULL)");
    $stmt->bindValue(":category_id",$_REQUEST["category_id"]);

    if(!$stmt->execute()) {
        echo "Error recreating category/tag connections."; die;
    } else {

        $stmt = $db->prepare("INSERT INTO category_tag (category_id,tag_id) VALUES (:category_id,:tag_id)");

        foreach($_REQUEST["ids"] as $id) {

            $stmt->bindValue(":category_id",$_REQUEST["category_id"]);
            $stmt->bindValue(":tag_id",intval($id));

            if(!$stmt->execute()) {
                echo "SQL Failure: ".$db->errorInfo()[2]." ";
            }
        }

        echo "success"; die;

    }
} else if(isset($_REQUEST["category_tag_connection"]) && $_REQUEST["category_tag_connection"]=="get") {

    // get the connections of this category
    $direct = array();

    $stmt = $db->prepare("SELECT tag_id FROM category_tag WHERE category_id = :category_id");
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
        $stmt2 = $db->prepare("SELECT tag_id FROM category_tag WHERE category_id IN (".implode(",",$parent_ids).")");

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