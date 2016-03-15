<?php


include("export-functions.php");

$columns = [
    "productMuid" => "",
    "articleMuid" => "",
    "articleWeight" => "",
    "articleVolume" => "",
    "articleArea" => "",
    "articleLength" => "",
    "articleUses" => "",
    "articleEanCode" => "",
    "articleBarCode" => "",
    "productImages" => "",
    "productName de_AT" => "",
    "productBrand de_AT" => "",
    "productCorporation de_AT" => "",
    "productDescription de_AT" => "",
    "articleUnit de_AT" => "container",
    "articleTagPaths" => ""
];

$defaultColumns = [
    "productNumber" => "",
    "productOverrideInsertNew" => "",
    "productDisplaySortValue" => "",
    "articleNumber" => "",
    "articlePrice" => "",
    "articleShippingWeight" => "",
    "articleShippingHeight" => "",
    "articleShippingWidth" => "",
    "articleShippingDepth" => "",
    "articleMinQuantity" => "1",
    "articleQuantitySteps" => "1",
    "articleStock" => "99999",
    "articleShowStock" => "FALSE",
    "articleMsrPrice" => "",
    "articleUnreducedPrice" => "",
    "articleUnreducedPriceType" => "",
    "articleMerchantInfo" => "",
    "articleSortValue" => "",
    "articleImages" => "",
    "articleCurrency" => "EUR",
    "articleTaxCategory" => "",
    "articleRestrictDeliveryToZone" => "",
    "articleNoticesJson de_AT" => "",
    "articlePosText de_AT" => "",
    "articleSelectorTags" => "",
    "articleMerchantTags" => ""
];

$stmt = $db->prepare('SELECT * FROM import WHERE id = :id');
$stmt->bindValue(":id",urldecode($_GET['export']));
$stmt->execute();
$import = $stmt->fetch();
 
$minstate = intval((isset($_GET['minstate'])) ? $_GET['minstate']:"0");
$maxstate = intval((isset($_GET['maxstate'])) ? $_GET['maxstate']:"20");

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id AND status >= :minstate AND status <= :maxstate ORDER BY id ASC');
$stmt->bindValue(":import_id",urldecode($_GET['export']));
$stmt->bindValue(":minstate",$minstate);
$stmt->bindValue(":maxstate",$maxstate);
$stmt->execute();
$fdata = $stmt->fetchAll();


// all data in state 10 gets state 15, dont touch otherwise
$stmt = $db->prepare('UPDATE fdata SET status=15 WHERE import_id = :import_id AND status =:finished');
$stmt->bindValue(":import_id",urldecode($_GET['export']));
$stmt->bindValue(":finished",10);
$stmt->execute();


    
// initialize array with column headings
$count = 1;
$column_headings = array();

// add header names to $column_headings
foreach($columns as $columnName => $dbColumnName) {
    array_push($column_headings, $columnName);
}
foreach($defaultColumns as $columnName => $defaultValue) {
    array_push($column_headings, $columnName);
}

$column_gatherer=array();
foreach($fdata as $row) {
    $article = array();

    foreach($columns as $columnName => $dbColumnName) {
        if ($dbColumnName === "") $dbColumnName = $columnName;

        $value = $row[$dbColumnName];
        $id = $row["id"];
        if ($columnName === "articleTagPaths") {
            $tagpath = "";

            // Categories
            $tagpath .= getCategoryExportPath($id);
            $tagpath .= getPreparedTagPathForRow($row);

            $article[$columnName] = $tagpath;
        } else if ($columnName === "productDescription de_AT") {
            $article[$columnName] = $value . getDescriptionAppendix($id);
        } else if ($columnName === "productImages") {
            $article[$columnName] = str_replace(",", ";", $value);
        } else if ($columnName === "articleWeight") {
            $article[$columnName] = "" . round(explode(" ", $value)[0], 12) . " kg";
        } else {
            $article[$columnName] = $value;
        }
    }

    foreach($defaultColumns as $columnName => $defaultValue) {
        $article[$columnName] = $defaultValue;
    }

    array_push($column_gatherer,$article);
}

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export-".$import['name']."-".$import['id'].".csv");
header("Pragma: no-cache");
header("Expires: 0");


// put out the original CSV
echo '"'.implode('","',$column_headings).'"
';


foreach($column_gatherer as $gath) {
    echo '"'.implode('","',$gath).'"
';
    
}

die;