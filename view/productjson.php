<?php

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE id = :id');
$stmt->bindValue(":id",$_GET['productjson']);
$stmt->execute();
$productdata = $stmt->fetch();

$imgs = preg_split( '/[;,]/', $productdata["productImages"] );
$encoded = array();

foreach ($imgs as $img) {
    $binfile = @json_decode(file_get_contents(BACKEND_URL . "backend/v2/merchant/binaryFile?muid=$img"));

    if(is_object($binfile)) {
        array_push($encoded, $binfile->generatedName);
    }
}

$productdata["productImages"] = implode(";", $encoded);

echo json_encode($productdata); die;