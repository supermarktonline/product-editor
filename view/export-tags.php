<?php

include("export-functions.php");

$stmt = $db->prepare('SELECT * FROM import WHERE id = :id');
$stmt->bindValue(":id",urldecode($_GET['export-tags']));
$stmt->execute();
$import = $stmt->fetch();


header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export-tags-".$import['name']."-".$import['id'].".csv");
header("Pragma: no-cache");
header("Expires: 0");
 
$minstate = intval((isset($_GET['minstate'])) ? $_GET['minstate']:"0");
$maxstate = intval((isset($_GET['maxstate'])) ? $_GET['maxstate']:"20");

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id AND status >= :minstate AND status <= :maxstate ORDER BY id ASC');
$stmt->bindValue(":import_id",urldecode($_GET['export-tags']));
$stmt->bindValue(":minstate",$minstate);
$stmt->bindValue(":maxstate",$maxstate);
$stmt->execute();
$fdata = $stmt->fetchAll();

    
// initialize array with column headings
$column_headings = array(
    "tagGroupingUid",
    "tagGroupingName de_AT",
    "tagGroupingName de_DE",
    "tagGroupingName en_US",
    "tagGroupingName es_ES",
    "tagGroupingName fr_FR",
    "tagGroupingDescription de_AT",
    "tagGroupingDescription de_DE",
    "tagGroupingDescription en_US",
    "tagGroupingDescription es_ES",
    "tagGroupingDescription fr_FR",
    "tagGroupingAutoTagNameCreation de_AT",
    "tagGroupingAutoTagNameCreation de_DE",
    "tagGroupingAutoTagNameCreation en_US",
    "tagGroupingAutoTagNameCreation es_ES",
    "tagGroupingAutoTagNameCreation fr_FR",
    "tagGroupingTagNumericalRequired",
    "tagUid",
    "tagName de_AT",
    "tagName de_DE",
    "tagName en_US",
    "tagName es_ES",
    "tagName fr_FR",
    "tagDescription de_AT",
    "tagDescription de_DE",
    "tagDescription en_US",
    "tagDescription es_ES",
    "tagDescription fr_FR",
    "tagSearchText de_AT",
    "tagSearchText de_DE",
    "tagSearchText en_US",
    "tagSearchText es_ES",
    "tagSearchText fr_FR",
    "tagNumericalValueRangeStart",
    "tagNumericalValueRangeEnd",
    "tagType"
);


$taglist = array();

foreach($fdata as $row) {

    // 1. get all the tags for each row
    $nutrient_path = getNutrientExportPath($row);
    $allergene_path = getAllergenExportPath($row);
    $sealetc_path = getSealEtcExportPath($row["id"]);


    // 2. Prepare single tag data
    $nutrient_fulltags = array_map('trim',explode(">>",$nutrient_path));
    $allergene_fulltags = array_map('trim',explode(">>",$allergene_path));
    $sealetc_fulltags = array_map('trim',explode(">>",$sealetc_path));

    $all_fulltags = array_filter(array_merge($nutrient_fulltags,$allergene_fulltags,$sealetc_fulltags));
    
    // 3. for each tag, generate a line for the output file
    foreach($all_fulltags as $tag) {
        
        $rdtag = array();
        
        $split = array_map('trim',explode(":",$tag));
        $group = $split[0];
        $val = $split[1];
        
        foreach($column_headings as $col) {
            
            // not ready
            if($col==="tagGroupingUid") {
                $rdtag[$col] = $group;
            } else if($col==="tagUid") {
                $rdtag[$col] = $tag;
            } else if($col==="tagType") {
                $rdtag[$col] = "ArticleDescribing";
            } else {
                $rdtag[$col] = "";
            }
            
        }
        array_push($taglist,$rdtag);
    }
}

// put out the original CSV
echo '"'.implode('","',$column_headings).'"
';

// eliminate duplicates
$taglist = array_map("unserialize", array_unique(array_map("serialize", $taglist)));

foreach($taglist as $gath) {
    echo '"'.implode('","',$gath).'"
';
    
}

die;