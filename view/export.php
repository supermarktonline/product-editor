<?php


header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export-".urldecode($_GET['export']).".csv");
header("Pragma: no-cache");
header("Expires: 0");
 

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id ORDER BY id ASC');
$stmt->bindValue(":import_id",urldecode($_GET['export']));
$stmt->execute();
$fdata = $stmt->fetchAll();

    
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
                // @TODO: Calculate new Tag Paths
                $article[$key] = $value;
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