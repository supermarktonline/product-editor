<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 9/9/16
 * Time: 6:26 PM
 */

echo '<p style="color:#191;">Note: Maybe you have to run this script twice until all warnings (except invalid row warnings) are zero. There should not be any errors after rerun.</p>';

require('../config-local.php');
require('../dal/db.php');

$dbobj = new DB();
$db = $dbobj->getDB();

/* Note: We have line breaks within the definition of the csv therefore a simple explode wont work */
$file = new SplFileObject('../../temp/gpc.en.csv');
$rows = array();
while (!$file->eof()) {
    array_push($rows,$file->fgetcsv());
}

if(count($rows)<2) {
    echo "Error finding or parsing CSV."; die;
}

$stmt_segment = $db->prepare("UPDATE category SET segment_description_en=:segment_description_en WHERE segment_code=:segment_code");
$stmt_family = $db->prepare("UPDATE category SET family_description_en=:family_description_en WHERE family_code=:family_code");
$stmt_class = $db->prepare("UPDATE category SET class_description_en=:class_description_en WHERE class_code=:class_code");
$stmt_brick = $db->prepare("UPDATE category SET brick_description_en=:brick_description_en,brick_definition_en=:brick_definition_en WHERE brick_code=:brick_code");
$stmt_attribute = $db->prepare("UPDATE taggroup SET muid=:muid,definition_en=:definition_en WHERE gs1_attribute_type_code=:gs1_attribute_type_code");
$stmt_value = $db->prepare("UPDATE tag SET muid=:muid,definition_en=:definition_en WHERE gs1_attribute_value_code=:gs1_attribute_value_code");

$in_brick = $db->prepare("INSERT INTO category (segment_code,family_code,class_code,brick_code) VALUES (:segment_code,:family_code,:class_code,:brick_code)");
$in_attribute = $db->prepare("INSERT INTO taggroup (gs1_attribute_type_code,muid,numerical_required,definition_en) VALUES (:gs1_attribute_type_code,:muid,:numerical_required,:definition_en)");
$in_value = $db->prepare("INSERT INTO tag (gs1_attribute_value_code,taggroup,muid,definition_en) VALUES (:gs1_attribute_value_code,(SELECT id FROM taggroup WHERE gs1_attribute_type_code=:gs1_attribute_type_code),:muid,:definition_en)");

$waroth=0;
$warseg=0;
$warfam=0;
$warcla=0;
$warbri=0;
$waratt=0;
$warval=0;

$erbri=0;
$eratt=0;
$erval=0;

$tree = array();

foreach($rows as $row) {

    $gs1id = @trim($row[0]);
    $type = @trim($row[1]);
    $name = @trim($row[2]);
    $definition = @trim($row[3]);
    $children = @array_flip(array_map('trim',explode(",",$row[4])));

    switch ($type):
        case "segment": {
            $stmt_segment->bindValue(":segment_description_en",$name);
            $stmt_segment->bindValue(":segment_code",$gs1id);

            $stmt_segment->execute();

            // tree building
            $tree[$gs1id] = $children;

            if($stmt_segment->rowCount()<1) {
                $warseg++;
            }
            break;
        }
        case "family": {

            $stmt_family->bindValue(":family_description_en",$name);
            $stmt_family->bindValue(":family_code",$gs1id);

            $stmt_family->execute();

            // tree building
            foreach($tree as $segid => $famid_ar) {
                foreach($famid_ar as $famid => $key) {
                    if($gs1id==$famid) {
                        $famid_ar[$gs1id] = $children;
                        $tree[$segid] = $famid_ar;
                        break 2;
                    }
                }
            }

            if($stmt_family->rowCount()<1) {
                $warfam++;
            }
            break;
        }
        case "class": {

            $stmt_class->bindValue(":class_description_en",$name);
            $stmt_class->bindValue(":class_code",$gs1id);

            $stmt_class->execute();

            // tree building
            foreach($tree as $segid => $famid_ar) {
                foreach($famid_ar as $famid => $class_ar) {
                    foreach($class_ar as $classid => $key) {
                        if($gs1id == $classid) {
                            $class_ar[$gs1id] = $children;
                            $tree[$segid][$famid] = $class_ar;
                            break 3;
                        }
                    }
                }
            }

            if($stmt_class->rowCount()<1) {
                $warcla++;
            }
            break;
        }
        case "brick": {

            $stmt_brick->bindValue(":brick_description_en",$name);
            $stmt_brick->bindValue(":brick_code",$gs1id);
            $stmt_brick->bindValue(":brick_definition_en",$definition);

            $stmt_brick->execute();

            // tree building
            foreach($tree as $segid => $famid_ar) {
                foreach($famid_ar as $famid => $class_ar) {
                    foreach($class_ar as $classid => $brick_ar) {
                        foreach($brick_ar as $brickid => $key) {
                            if($gs1id == $brickid) {
                                $brick_ar[$gs1id] = $children;
                                $tree[$segid][$famid][$classid] = $brick_ar;

                                if($stmt_brick->rowCount()<1) {
                                    // try to insert brick
                                    $in_brick->bindValue(":segment_code",$segid);
                                    $in_brick->bindValue(":family_code",$famid);
                                    $in_brick->bindValue(":class_code",$classid);
                                    $in_brick->bindValue(":brick_code",$brickid);

                                    $suc = $in_brick->execute();

                                    if(!$suc) {
                                        $erbri++;
                                    }
                                }
                                break 4;
                            }
                        }
                    }
                }
            }

            if($stmt_brick->rowCount()<1) {
                $warbri++;
            }
            break;
        }
        case "attribute": {

            $stmt_attribute->bindValue(":muid",$name);
            $stmt_attribute->bindValue(":definition_en",$definition);
            $stmt_attribute->bindValue(":gs1_attribute_type_code",$gs1id);

            $stmt_attribute->execute();

            // tree building
            foreach($tree as $segid => $famid_ar) {
                foreach($famid_ar as $famid => $class_ar) {
                    foreach($class_ar as $classid => $brick_ar) {
                        foreach($brick_ar as $brickid => $attr_ar) {
                            foreach($attr_ar as $attrid => $key) {
                                if ($gs1id == $attrid) {
                                    $attr_ar[$gs1id] = $children;
                                    $tree[$segid][$famid][$classid][$brickid] = $attr_ar;
                                    break 5;
                                }
                            }
                        }
                    }
                }
            }

            // if there was no update, the taggroup was missing
            if($stmt_attribute->rowCount()<1) {
                $in_attribute->bindValue(":muid",$name);
                $in_attribute->bindValue(":definition_en",$definition);
                $in_attribute->bindValue(":gs1_attribute_type_code",$gs1id);
                $in_attribute->bindValue(":numerical_required",false,PDO::PARAM_BOOL);

                $suc = $in_attribute->execute();

                if(!$suc) {
                    print_r($in_attribute->errorinfo()); echo '<br/>';
                    $eratt++;
                }

                $waratt++;
            }
            break;
        }
        case "value": {

            $stmt_value->bindValue(":muid",$name);
            $stmt_value->bindValue(":definition_en",$definition);
            $stmt_value->bindValue(":gs1_attribute_value_code",$gs1id);

            $stmt_value->execute();

            if($stmt_value->rowCount()<1) {
                foreach($tree as $segid => $famid_ar) {
                    foreach($famid_ar as $famid => $class_ar) {
                        foreach($class_ar as $classid => $brick_ar) {
                            foreach($brick_ar as $brickid => $attr_ar) {
                                foreach($attr_ar as $attrid => $val_ar) {
                                    if(is_array($val_ar)) {
                                        foreach ($val_ar as $valid => $key) {
                                            if ($valid == $gs1id) {

                                                $in_value->bindValue(":muid", $name);
                                                $in_value->bindValue(":definition_en", $definition);
                                                $in_value->bindValue(":gs1_attribute_value_code", $gs1id);
                                                $in_value->bindValue(":gs1_attribute_type_code", $attrid);

                                                $suc = $in_value->execute();

                                                if (!$suc) {
                                                    print_r($in_value->errorinfo()); echo '<br/>';
                                                    $erval++;
                                                }
                                                break 6;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $warval++;
            }
            break;
        }
        default: {
            $waroth++;
            break;
        }
    endswitch;
}


echo "Segement warnings: ".$warseg.'<br/>';
echo "Family warnings: ".$warfam.'<br/>';
echo "Class warnings: ".$warcla.'<br/>';
echo "Brick warnings: ".$warbri.'<br/>';
echo "Attribute warnings: ".$waratt.'<br/>';
echo "Value warnings: ".$warval.'<br/>';
echo "Invalid row warnings: ".$waroth.'<br/>';

echo "Total warnings: ".($warseg+$warfam+$warcla+$warbri+$waratt+$warval+$waroth).'<br/>';

echo "Total brick errors: ".$erbri.'<br/>';
echo "Total attribute errors: ".$eratt.'<br/>';
echo "Total value errors: ".$erval.'<br/>';

echo "BASE UPDATES ARE DONE: UPDATE category/tag (= brick/attribute/value) connections now.".'<br/>';


$excon = 0;
$newcon = 0;

$in_attrcon = $db->prepare("INSERT INTO category_tag(category_id,tag_id) VALUES ( (SELECT gid FROM category WHERE brick_code=:brick_code) , (SELECT id FROM tag WHERE gs1_attribute_value_code=:gs1_attribute_value_code))");

foreach($tree as $segid => $famid_ar) {
    foreach($famid_ar as $famid => $class_ar) {
        foreach($class_ar as $classid => $brick_ar) {
            foreach($brick_ar as $brickid => $attr_ar) {
                foreach($attr_ar as $attrid => $val_ar) {
                    if(is_array($val_ar)) {
                        foreach ($val_ar as $valid => $i) {
                            $in_attrcon->bindValue(":brick_code", $brickid);
                            $in_attrcon->bindValue(":gs1_attribute_value_code", $valid);

                            $suc = $in_attrcon->execute();

                            if (!$suc) {
                                $excon++;
                            } else {
                                $newcon++;
                            }
                        }
                    }
                }
            }
        }
    }
}

echo "Existing connections: ".$excon.'<br/>';
echo "New connections: ".$newcon.'<br/>';

echo "Script finished.";