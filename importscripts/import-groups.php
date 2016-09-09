<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 12/17/15
 * Time: 7:57 PM
 *
 * Fast and ugly import for the existing tag groups. If the muid already exists the import will fail cause of the unique constraint.
 *
 */

require('../config-local.php');
require('../dal/db.php');

$dbobj = new DB();
$db = $dbobj->getDB();

$file = file_get_contents('../temp/groups.csv', true);

$rows = explode("\n",$file);

foreach($rows as $row) {
    $res = trim($row);
    $a = array_map('trim',explode(";",$row));

    $a[0] = trim($a[0],'"');
    $a[1] = trim($a[1], '"');

    if($a[0]!="" && $a[1]!="") {
        $stmt = $db->prepare("INSERT INTO taggroup (muid,name_de) VALUES (:muid,:name_de)");
        $stmt->bindValue(":muid",$a[0]);
        $stmt->bindValue(":name_de",$a[1]);

        $stmt->execute();

    }
}

echo "Script finished.";