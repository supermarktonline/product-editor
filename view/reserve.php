<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 2/26/16
 * Time: 7:46 PM
 */


$pid = intval($_REQUEST["pid"]);
$action = boolval($_REQUEST["reserve"]);
$user = $_REQUEST["user"];

$stmt = $db->prepare('SELECT * FROM fdata WHERE id = :id');
$stmt->bindValue(":id",$pid);
$stmt->execute();
$product = $stmt->fetch();


if($stmt->rowCount() > 0) {

    $cur_user = $product->reserved_by;

    if($action) {

        if($cur_user !="") {
            echo "Error: This product is already reserved by ".$cur_user.".";
        } else {

            $stmt = $db->prepare('UPDATE fdata set reserved_by = :user WHERE id = :id');
            $stmt->bindValue(":id",$pid);
            $stmt->bindValue(":user",$user);
            $stmt->execute();

            echo "success";
        }

        // unreserve
    } else {
            $stmt = $db->prepare('UPDATE fdata set reserved_by = :empty WHERE id = :id');
            $stmt->bindValue(":id",$pid);
            $stmt->bindValue(":empty","");
            $stmt->execute();

            echo "success";
    }


} else {
    echo "Error: This product was not found in the database.";
}
