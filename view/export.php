<?php


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
    "productGpcBrick" => "category"
];

$defaultColumns = [
    "productNumber" => "",
    "productOverrideInsertNew" => "",
    "productDisplaySortValue" => "",
    "articleNumber" => "",
    "articlePricings" => "",
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
    "articleTaxCategory" => "",
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
foreach ($fdata as $row) {
    $article = array();

    // undo bug where for instance «9 g» has been converted to 0.009000000000000001 kg
    // also don't print 0 values
    $articleWeight = $row['articleWeight'];
    if ($articleWeight != "") {
        if ($articleWeight == "0 kg") {
            $row['articleWeight'] = "";
        } else {
            $row['articleWeight'] = "" . round(explode(" ", $articleWeight)[0], 12) . " kg";
        }
    }
    $articleVolume = $row['articleVolume'];
    if ($articleVolume != "") {
        if ($articleVolume == "0 l") {
            $row['articleVolume'] = "";
        } else {
            $row['articleVolume'] = "" . round(explode(" ", $articleVolume)[0], 12) . " l";
        }
    }

    foreach ($columns as $columnName => $dbColumnName) {
        if ($dbColumnName === "") $dbColumnName = $columnName;

        $value = $row[$dbColumnName];
        $id = $row["id"];
        if ($columnName === "productMuid") {
            $article[$columnName] = $row["bestandsfuehrer"];
        } else if ($columnName === "articleMuid") {
            $article[$columnName] = $row["bestandsfuehrer"];
        } else if ($columnName === "articleTagPaths") {
            $tagpath = "";

            // Categories
            $tagpath .= getCategoryExportPath($id);
            $tagpath .= getPreparedTagPathForRow($row);

            $article[$columnName] = quoteForCsv($tagpath);
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