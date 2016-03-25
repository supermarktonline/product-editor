<?php


include("export-functions.php");

$stmt = $db->prepare('SELECT * FROM import WHERE id = :id');
$stmt->bindValue(":id",urldecode($_GET['export-tags']));
$stmt->execute();
$import = $stmt->fetch();
 
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
    "tagGroupingName de_DE",
    "tagGroupingDescription de_DE",
    "tagGroupingAutoTagNameCreation de_DE",
    "tagGroupingTagNumericalRequired",
    "tagGroupingGpcId",
    "tagUid",
    "tagName de_DE",
    "tagDescription de_DE",
    "tagSearchText de_DE",
    "tagNumericalValueRangeStart",
    "tagNumericalValueRangeEnd",
    "tagType",
    "gpcId",
    "googleTaxonomyId"
);

$taglist = array();

foreach($fdata as $row) {
    $taglist = array_merge($taglist,  getAllTagsForRow($row));
}

// http://www.jonasjohn.de/snippets/php/trim-array.htm
function trim_r($arr) {
    return is_array($arr) ? array_map('trim_r', $arr) : trim($arr);
}

// trim all values
$taglist = trim_r($taglist);

// eliminate all duplicate tags
$taglist = array_map("unserialize", array_unique(array_map("serialize", $taglist)));

// eliminate grouping properties for each grouping which occures more than once
$taglist = tagGroupingFilterRemoveDuplicate($taglist);

$resempty = function($array,$key) {
    if(array_key_exists($key,$array)) {
        return $array[$key];
    }
    return "";
};

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export-tags-".$import['name']."-".$import['id'].".csv");
header("Pragma: no-cache");
header("Expires: 0");


// put out the original CSV
echo '"'.implode('","',$column_headings).'"
';



// taglist contains all tags
foreach($taglist as $tl) {
    
    $gather = array();
    foreach($column_headings as $ch) {
        $gather[$ch] = $resempty($tl,$ch);
    }

    echo '"';
    echo implode('","', $gather);
    echo '"';
    echo '
';
    
}

die;