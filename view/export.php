<?php

// Since we passed ~5000 products, the export needs more memory than PHP might otherwise provide. Other optimizations
// may be possible and this is only a quick fix.
ini_set('memory_limit', '-1');

include("export-functions.php");

$columns = [
    "productMuid" => "",
    "articleMuid" => "",
    "productBrand de_AT" => "",
    "productName de_AT" => "",
    "articleWeight" => "",
    "articleVolume" => "",
    "articleArea" => "",
    "articleLength" => "",
    "articleUses" => "",
    "articleTradeItemIds" => "articleBarCode",
    "productImages" => "",
    "productFamily de_AT" => "",
    "productCorporation de_AT" => "",
    "productDescription de_AT" => "",
    "articleUnit de_AT" => "container",
    "articleTagPaths" => "",
    "productEditorId" => "id",
    "productGpcBrick" => "category",
    "articlePricings" => "articlePrice",
    "articleTaxCategory" => ""
];

$defaultColumns = [
    "productNumber" => "",
    "productOverrideInsertNew" => "",
    "productDisplaySortValue" => "",
    "articleNumber" => "",
    "articleDescription de_AT" => "",
    "articleShippingWeight" => "",
    "articleShippingHeight" => "",
    "articleShippingWidth" => "",
    "articleShippingDepth" => "",
    "articleMinQuantity" => "1",
    "articleQuantitySteps" => "1",
    "articleShared" => "TRUE",
    "articleMerchantInfo" => "",
    "articleSortValue" => "",
    "articleImages" => "",
    "articleCurrency" => "EUR",
    "articleRestrictDeliveryToZone" => "",
    "articleNoticesJson de_AT" => "",
    "articlePosText de_AT" => "",
    "articleSelectorTags" => "",
    "articleMerchantTags" => ""
];

$editorColumns = [
    "name" => "Stat Editor Name",
    "count" => "Stat Inhaltsstoffe Anzahl"
];

if ($_GET['export'] !== "ALL") {
    $stmt = $db->prepare('SELECT * FROM import WHERE id = :id');
    $stmt->bindValue(":id", urldecode($_GET['export']));
    $stmt->execute();
    $import = $stmt->fetch();
}

$minstate = intval((isset($_GET['minstate'])) ? $_GET['minstate'] : "0");
$maxstate = intval((isset($_GET['maxstate'])) ? $_GET['maxstate'] : "20");

// query the list of the desired import
if ($_GET['export'] === "ALL") {
    $stmt = $db->prepare('SELECT * FROM fdata WHERE status >= :minstate AND status <= :maxstate ORDER BY id ASC');
} else {
    $stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id AND status >= :minstate AND status <= :maxstate ORDER BY id ASC');
    $stmt->bindValue(":import_id", urldecode($_GET['export']));
}
$stmt->bindValue(":minstate", $minstate);
$stmt->bindValue(":maxstate", $maxstate);
$stmt->execute();
$fdata = $stmt->fetchAll();

// initialize array with column headings
$count = 1;
$column_headings = array();

// add header names to $column_headings
foreach ($columns as $columnName => $dbColumnName) {
    array_push($column_headings, $columnName);
}
foreach ($defaultColumns as $columnName => $defaultValue) {
    array_push($column_headings, $columnName);
}
foreach ($editorColumns as $intName => $columnName) {
    array_push($column_headings, $columnName);
}

$column_gatherer = array();

function sanitizeValue($row, $field)
{
    if ($row[$field] != "") {
        $parts = explode(" ", $row[$field]);

        if ($parts[0] == "0") {
            $row[$field] = "";
            return $row;
        } else {
            $row[$field] = "" . round($parts[0], 12) . " " . $parts[1];
            return $row;
        }
    }
    return $row;
}

foreach ($fdata as $row) {
    $article = array();

    // undo bug where for instance «9 g» has been converted to 0.009000000000000001 kg
    // also don't print 0 values
    $row = sanitizeValue($row, 'articleWeight');
    $row = sanitizeValue($row, 'articleVolume');

    foreach ($columns as $columnName => $dbColumnName) {
        if ($dbColumnName === "") $dbColumnName = $columnName;

        $value = $row[$dbColumnName];
        $id = $row["id"];
        if ($columnName === "productMuid") {
            $article[$columnName] = $row["bestandsfuehrer"];
        } else if ($columnName === "articleMuid") {
            $article[$columnName] = $row["bestandsfuehrer"];
        } else if ($columnName === "articleTagPaths") {
            $tags = implode(";", array_merge(getCategoryExportPath($id), getTagIDsForRow($row)));
            $article[$columnName] = quoteForCsv($tags);
        } else if ($columnName === "productDescription de_AT") {
            $beschreibung = $value ? "-----------\nBeschreibung\n======\n" . $value . "\n" : "";
            $article[$columnName] = quoteForCsv($beschreibung . getDescriptionAppendix($id));
        } else if ($columnName === "productImages") {
            $article[$columnName] = quoteForCsv(str_replace(",", ";", $value));
        } else if ($columnName === "articleUnit de_AT" && $value == "") {
            $article[$columnName] = quoteForCsv("Stück");
        } else if ($columnName === "productGpcBrick") {
            $cat = getCategory($id);
            $article[$columnName] = $cat["brick_code"];
        } else {
            $article[$columnName] = quoteForCsv($value);
        }
    }

    foreach ($defaultColumns as $columnName => $defaultValue) {
        $article[$columnName] = quoteForCsv($defaultValue);
    }

    foreach ($editorColumns as $c => $name) {
        if ($c === "name") {
            $article[$name] = quoteForCsv($row["reserved_by"]);
        } else if ($c === "count") {
            $article[$name] = quoteForCsv(countIngredients($id));
        }
    }

    array_push($column_gatherer, $article);
}


header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=articles-" . (isset($import) ? $import['name'] : "ALL") . "-" . date('Ymd') . ".csv");
header("Pragma: no-cache");
header("Expires: 0");


// put out the original CSV
echo '"' . implode('","', $column_headings) . '"
';


foreach ($column_gatherer as $gath) {
    echo '"' . implode('","', $gath) . '"
';

}

die;