<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 12/22/15
 * Time: 4:41 PM
 */
global $db;

if($_REQUEST["taggroup"]=="create") {

    if(!filter_var($_REQUEST["muid"], FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^((?=[ -~])[^:])+$/"))) || strlen($_REQUEST["name"]) < 1 ) {
        echo json_encode(array("error"=>"Error: MUID must be ASCII and not empty, name must not be empty.")); die;
    }

    $stmt = $db->prepare("INSERT INTO taggroup (muid,name) VALUES (:muid,:name)");
    $stmt->bindValue(":muid",ucfirst($_REQUEST["muid"]));
    $stmt->bindValue(":name",ucfirst($_REQUEST["name"]));

    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2]." (possibly duplicate?)")); die;
    } else {

        $stmt = $db->prepare("SELECT * from taggroup WHERE name = :name AND muid = :muid");
        $stmt->bindValue(":muid",ucfirst($_REQUEST["muid"]));
        $stmt->bindValue(":name",ucfirst($_REQUEST["name"]));
        $stmt->execute();

        echo json_encode($stmt->fetch());
    }

}