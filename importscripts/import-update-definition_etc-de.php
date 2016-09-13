<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 9/9/16
 * Time: 6:26 PM
 */

require('../config-local.php');
require('../dal/db.php');

$dbobj = new DB();
$db = $dbobj->getDB();

/* Note: We have line breaks within the definition of the csv therefore a simple explode wont work */
$file = new SplFileObject('../../temp/gpc.de.csv');
$rows = array();
while (!$file->eof()) {
    array_push($rows,$file->fgetcsv());
}

if(count($rows)<2) {
    echo "Error finding or parsing CSV."; die;
}

$stmt_segment = $db->prepare("UPDATE category SET segment_description_de=:segment_description_de WHERE segment_code=:segment_code");
$stmt_family = $db->prepare("UPDATE category SET family_description_de=:family_description_de WHERE family_code=:family_code");
$stmt_class = $db->prepare("UPDATE category SET class_description_de=:class_description_de WHERE class_code=:class_code");
$stmt_brick = $db->prepare("UPDATE category SET brick_description_de=:brick_description_de,brick_definition_de=:brick_definition_de WHERE brick_code=:brick_code");
$stmt_attribute = $db->prepare("UPDATE taggroup SET name_de=:name_de,definition_de=:definition_de WHERE gs1_attribute_type_code=:gs1_attribute_type_code");
$stmt_value = $db->prepare("UPDATE tag SET name_de=:name_de,definition_de=:definition_de WHERE gs1_attribute_value_code=:gs1_attribute_value_code");

$waroth=0;
$warseg=0;
$warfam=0;
$warcla=0;
$warbri=0;
$waratt=0;
$warval=0;

foreach($rows as $row) {

    $gs1id = @trim($row[0]);
    $type = @trim($row[1]);
    $name = @trim($row[2]);
    $definition = @trim($row[3]);
    $children = @trim($row[4]);

    switch ($type):
        case "segment": {
            $stmt_segment->bindValue(":segment_description_de",$name);
            $stmt_segment->bindValue(":segment_code",$gs1id);

            $stmt_segment->execute();

            if($stmt_segment->rowCount()<1) {
                echo "Warning: No rows affected in update statement for segment: ".print_r($row,true).'<br/>';
                $warseg++;
            }
            break;
        }
        case "family": {

            $stmt_family->bindValue(":family_description_de",$name);
            $stmt_family->bindValue(":family_code",$gs1id);

            $stmt_family->execute();

            if($stmt_family->rowCount()<1) {
                echo "Warning: No rows affected in update statement for family: ".print_r($row,true).'<br/>';
                $warfam++;
            }
            break;
        }
        case "class": {

            $stmt_class->bindValue(":class_description_de",$name);
            $stmt_class->bindValue(":class_code",$gs1id);

            $stmt_class->execute();

            if($stmt_class->rowCount()<1) {
                echo "Warning: No rows affected in update statement for class: ".print_r($row,true).'<br/>';
                $warcla++;
            }
            break;
        }
        case "brick": {

            $stmt_brick->bindValue(":brick_description_de",$name);
            $stmt_brick->bindValue(":brick_code",$gs1id);
            $stmt_brick->bindValue(":brick_definition_de",$definition);

            $stmt_brick->execute();

            if($stmt_brick->rowCount()<1) {
                echo "Warning: No rows affected in update statement for brick: ".print_r($row,true).'<br/>';
                $warbri++;
            }
            break;
        }
        case "attribute": {

            $stmt_attribute->bindValue(":name_de",$name);
            $stmt_attribute->bindValue(":definition_de",$definition);
            $stmt_attribute->bindValue(":gs1_attribute_type_code",$gs1id);

            $stmt_attribute->execute();

            if($stmt_attribute->rowCount()<1) {
                echo "Warning: No rows affected in update statement for attribute (tag group): ".print_r($row,true).'<br/>';
                $waratt++;
            }
            break;
        }
        case "value": {

            $stmt_value->bindValue(":name_de",$name);
            $stmt_value->bindValue(":definition_de",$definition);
            $stmt_value->bindValue(":gs1_attribute_value_code",$gs1id);

            $stmt_value->execute();

            if($stmt_value->rowCount()<1) {
                echo "Warning: No rows affected in update statement for value (tag): ".print_r($row,true).'<br/>';
                $warval++;
            }
            break;
        }
        default: {
            echo "Row not imported (invalid type): ".print_r($row,true).'<br/>';
            $waroth++;
            break;
        }
    endswitch;
}

echo "Segement failures: ".$warseg.'<br/>';
echo "Family failures: ".$warfam.'<br/>';
echo "Class failures: ".$warcla.'<br/>';
echo "Brick failures: ".$warbri.'<br/>';
echo "Attribute failures: ".$waratt.'<br/>';
echo "Value failures: ".$warval.'<br/>';
echo "Invalid row failures: ".$waroth.'<br/>';

echo "Total failures: ".($warseg+$warfam+$warcla+$warbri+$waratt+$warval+$waroth).'<br/>';
echo "Script finished.";