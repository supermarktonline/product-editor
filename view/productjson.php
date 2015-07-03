<?php

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE id = :id');
$stmt->bindValue(":id",$_GET['productjson']);
$stmt->execute();
$productdata = $stmt->fetch();

echo json_encode($productdata); die;