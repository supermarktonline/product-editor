<?php


include("export-functions.php");

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export-".urldecode($_GET['export']).".csv");
header("Pragma: no-cache");
header("Expires: 0");
 
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

foreach($fdata[0] as $key  => $value) {
    if($count > NUM_COLS_BEFORE && $count <= NUM_COLS_BEFORE+NUM_IMPORT_COLS) {
        array_push($column_headings,$key);
    }

    $count++;
}

$column_gatherer=array();
foreach($fdata as $row) {
    
    // a row is an associative array of cols and val
    $count = 1;
    $article = array();
    foreach($row as $key  => $value) {
        if($count > NUM_COLS_BEFORE && $count <= NUM_COLS_BEFORE+NUM_IMPORT_COLS) {
            
            if($key=="articletagpaths") {
                
                $tagpath = "";
                
                // Categories
                $tagpath .= getCategoryExportPath($row["id"]);
                
                // Nutrients
                $tagpath .= getNutrientExportPath($row);
                
                // allergene
                $tagpath .= getAllergenExportPath($row);
                
                // gütesiegel etc.
                $tagpath .= getSealEtcExportPath($row["id"]);
                
                $article[$key] = $tagpath;
                
            } else if(strtolower($key)==strtolower("productdescription de_at")) {
                $article[$key] = $value.getIngredientExport($row["id"]);
            } else {
                $article[$key] = $value;
            }
        }
        $count++;
    }
    array_push($column_gatherer,$article);
}

// put out the original CSV
echo '"'.implode('","',$column_headings).'"
';


foreach($column_gatherer as $gath) {
    echo '"'.implode('","',$gath).'"
';
    
}

die;