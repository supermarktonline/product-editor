<?php
/**
 * Updating Products.
 */

global $db;

if(!isset($_POST["id"]) || intval($_POST["id"])<1 || intval($_POST["id"])>PHP_INT_MAX ) {
    die;
}

// check if state allowed
$desired_state = intval($_POST["status"]);
if($desired_state > 9) {

    $dissallow = false;

    // check required fields
    if(!Validator::validateString($_POST["productName___de_AT"],array("nemp"=>""))) {
        echo "Dieser Status ist nicht erlaubt: Produktname fehlt.<br/>";
        $dissallow = true;
    }

    if(!Validator::validateString($_POST["productCorporation___de_AT"],array("nemp"=>""))) {
        echo "Dieser Status ist nicht erlaubt: Firmenname/Hersteller fehlt.<br/>";
        $dissallow = true;
    }

    if(!Validator::validateString($_POST["productBrand___de_AT"],array("nemp"=>""))) {
        echo "Dieser Status ist nicht erlaubt: Marke fehlt.<br/>";
        $dissallow = true;
    }

    // check if ingredients are connected
    $stmt = $db->prepare("SELECT COUNT(*) FROM fdata_ingredient WHERE fdata_id= :fdata_id");
    $stmt->bindValue(":fdata_id",$_POST["id"]);
    $stmt->execute();

    if(!Validator::validateInt(intval($_POST["category"]),array("min"=>1))) {
        echo "Dieser Status ist nicht erlaubt: Kategorie muss ausgewählt sein.<br/>";
        $dissallow = true;
    }

    if($dissallow) {
        die;
    }
}


// replace whitespace for column with whitepace in it
$wrep = function($key) {
    return str_replace("___"," ",$key);
};

$update_query = 'UPDATE fdata SET ';

$first = false;
foreach($_POST AS $key => $value) {
    if($key!=="id") {
        $update_query .= ($first) ? ",":"";
        $update_query .= '"'.$wrep($key).'" = :'.$key;
        $first = true;
    }
}

$update_query .= " WHERE id = :id";

$stmt = $db->prepare($update_query);


foreach($_POST AS $key => $value) {
    $stmt->bindValue(":".$key,$value);
}

if(!$stmt->execute()) {
    echo "SQL Failure: ".$db->errorInfo()[2]; die;
}

echo "success"; die;