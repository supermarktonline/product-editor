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

} else if($_REQUEST["taggroup"]=="delete") {

    // if a taggroup is already connected deletion should be impossible because of "on delete restrict" in the database
    $stmt = $db->prepare("DELETE FROM taggroup WHERE id=:id");
    $stmt->bindValue(":id",$_REQUEST["id"],PDO::PARAM_INT);
    $stmt->execute();

    if(!$stmt->execute()) {
        echo json_encode(array("error"=>"SQL Failure: ".$db->errorInfo()[2])); die;
    } else {
        echo "success"; die;
    }

    echo json_encode(array("error"=>"Taggroup deletion is not allowed. The Object is connected to some article.")); die;
}