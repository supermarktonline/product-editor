<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 1/8/16
 * Time: 4:55 PM
 */

/*
 * Execute an import of GS1 english CSV
 */

if(isset($_POST['newimpgpr']) && $_POST['newimpgpr']=="doit") {

    if(isset($_FILES["impfilegpr"])) {
        if ($_FILES["impfilegpr"]["error"] > 0) {
            array_push($user_messages,array("error",$_FILES["file"]["error"]));
        } else {

            if($_FILES["impfilegpr"]["type"]=="text/csv") {

                $updates = false; // set this to false, if you want to insert only


                $category_already_existed = 0;
                $category_didnt_exist = 0;
                $category_insert_errors = 0;
                $category_update_errors = 0;

                $taggroup_already_existed = 0;
                $taggroup_didnt_exist = 0;
                $taggroup_insert_errors = 0;
                $taggroup_update_errors = 0;

                $tag_already_existed = 0;
                $tag_didnt_exist = 0;
                $tag_insert_errors = 0;
                $tag_update_errors = 0;

                $connection_already_existed = 0;
                $connection_created = 0;


                if (($handle = fopen($_FILES["impfilegpr"]["tmp_name"], "r")) !== FALSE) {
                    $findid = $db->prepare("SELECT gid FROM category WHERE segment_code=:segment_code AND family_code=:family_code AND class_code=:class_code AND brick_code=:brick_code");
                    $upCategory = $db->prepare("UPDATE category SET segment_description_en=:segment_description_en, family_description_en=:family_description_en, class_description_en=:class_description_en, brick_description_en=:brick_description_en WHERE gid=:id");
                    $inCategory = $db->prepare("INSERT INTO category(segment_code,family_code,class_code,brick_code,segment_description_en,family_description_en,class_description_en,brick_description_en)
                              VALUES (:segment_code,:family_code,:class_code,:brick_code,:segment_description_en,:family_description_en,:class_description_en,:brick_description_en)");
                    $dbTgid = $db->prepare("SELECT id FROM taggroup WHERE gs1_attribute_type_code=:gs1_attribute_type_code");
                    $inTg = $db->prepare("INSERT INTO taggroup(gs1_attribute_type_code,muid) VALUES (:gs1_attribute_type_code,:muid)");
                    $dbTid = $db->prepare("SELECT id FROM tag WHERE gs1_attribute_value_code=:gs1_attribute_value_code AND taggroup=:taggroup");
                    $upTag = $db->prepare("UPDATE tag SET muid=:muid WHERE id=:id");
                    $upTg = $db->prepare("UPDATE taggroup SET muid=:muid WHERE id=:id");
                    $inTag = $db->prepare("INSERT INTO tag(gs1_attribute_value_code,taggroup,muid,type) VALUES (:gs1_attribute_value_code,:taggroup,:muid,:type)");
                    $connect = $db->prepare("INSERT INTO category_tag (category_id,tag_id) VALUES (:category_id,:tag_id)");

                    $dbRowCounter = 0;

                    // length found by    wc -L GS1\ Combined\ Published*.csv
                    while (($data = fgetcsv($handle, 400, ",")) !== FALSE) {
                        ++$dbRowCounter;
                        if ($dbRowCounter % 10000 == 0) error_log("Processing row: " . $dbRowCounter);
                        
                        // skip header-row
                        if ($dbRowCounter == 1 && $data[0] == "Segment Code") continue;


                        // category specific
                        $segment_code = $data[0];
                        $family_code = $data[2];
                        $class_code = $data[4];
                        $brick_code = $data[6];

                        $segment_description_en = $data[1];
                        $family_description_en = $data[3];
                        $class_description_en = $data[5];
                        $brick_description_en = $data[7];

                        // tag group specific
                        $tg_gs1_attribute_type_code = $data[8];
                        $tg_muid = $data[9];

                        // tag specific
                        $t_gs1_attribute_value_code = $data[10];
                        $t_muid = $data[11];


                        // 1. Upsert the Category
                        $findid->bindValue(":segment_code",$segment_code);
                        $findid->bindValue(":family_code",$family_code);
                        $findid->bindValue(":class_code",$class_code);
                        $findid->bindValue(":brick_code",$brick_code);

                        $findid->execute();

                        $id = intval($findid->fetchColumn(0));

                        if($id>0) {
                            // this gs1 category exists already - update the english names
                            $category_already_existed++;

                            if($updates) {
                                $upCategory->bindValue(":segment_description_en",$segment_description_en);
                                $upCategory->bindValue(":family_description_en",$family_description_en);
                                $upCategory->bindValue(":class_description_en",$class_description_en);
                                $upCategory->bindValue(":brick_description_en",$brick_description_en);
                                $upCategory->bindValue(":id",$id);

                                $upsuc = $upCategory->execute();

                                if(!$upsuc) {
                                    error_log("Could not update category: $id $segment_description_en | $family_description | $class_description_en | $brick_description_en");
                                    $category_update_errors++;
                                }
                            }
                        } else {
                            $category_didnt_exist++;

                            $inCategory->bindValue(":segment_code",$segment_code);
                            $inCategory->bindValue(":family_code",$family_code);
                            $inCategory->bindValue(":class_code",$class_code);
                            $inCategory->bindValue(":brick_code",$brick_code);
                            $inCategory->bindValue(":segment_description_en",$segment_description_en);
                            $inCategory->bindValue(":family_description_en",$family_description_en);
                            $inCategory->bindValue(":class_description_en",$class_description_en);
                            $inCategory->bindValue(":brick_description_en",$brick_description_en);

                            $insuc = $inCategory->execute();

                            if(!$insuc) {
                                error_log("Could not insert category: $segment_code | $family_code | $class_code | $brick_code | $segment_description_en | $family_description_en | $class_description_en | $brick_description_en");
                                $category_insert_errors++;
                            }
                            $id= intval($db->lastInsertId());
                        }

                        if ($tg_gs1_attribute_type_code == "") continue;

                        // if not exists insert taggroup, otherwise get its id
                        $dbTgid->bindValue(":gs1_attribute_type_code",$tg_gs1_attribute_type_code);
                        $dbTgid->execute();

                        $tgid = intval($dbTgid->fetchColumn(0));

                        if($tgid>0) {
                            $taggroup_already_existed++;
                            if($updates) {
                                $upTg->bindValue(":muid",$tg_muid);
                                $upTg->bindValue(":id",$tgid);

                                $upsuc = $upTg->execute();

                                if(!$upsuc) {
                                    error_log("Could not update taggroup: $tgid | $tg_muid");
                                    $taggroup_update_errors++;
                                }
                            }
                        } else {
                            $taggroup_didnt_exist++;
                            $inTg->bindValue(":gs1_attribute_type_code",$tg_gs1_attribute_type_code);
                            $inTg->bindValue(":muid",$tg_muid);

                            $insuc = $inTg->execute();

                            if(!$insuc) {
                                error_log("Could not insert taggroup: $tg_muid | $tg_gs1_attribute_type_code");
                                error_log("Data row ($dbRowCounter): " . implode("|", $data));
                                $taggroup_insert_errors++;
                            }
                            $tgid= intval($db->lastInsertId());
                        }

                        // if not exists insert tag, otherwise get its id
                        $dbTid->bindValue(":gs1_attribute_value_code",$t_gs1_attribute_value_code);
                        $dbTid->bindValue(":taggroup",$tgid);
                        $dbTid->execute();

                        $tid = intval($dbTid->fetchColumn(0));

                        if($tid>0) {
                            $tag_already_existed++;
                            if($updates) {
                                $upTag->bindValue(":muid",$t_muid);
                                $upTag->bindValue(":id",$tid);

                                $upsuc = $upTag->execute();

                                if(!$upsuc) {
                                    error_log("Could not update tag: $tid | $t_muid");
                                    $tag_update_errors++;
                                }
                            }
                        } else {
                            $tag_didnt_exist++;
                            $inTag->bindValue(":gs1_attribute_value_code",$t_gs1_attribute_value_code);
                            $inTag->bindValue(":taggroup",$tgid);
                            $inTag->bindValue(":muid",$t_muid);
                            $inTag->bindValue(":type",NULL);

                            $insuc = $inTag->execute();

                            if(!$insuc) {
                                error_log("Could not insert tag: $t_muid | $tgid | $t_gs1_attribute_value_code");
                                $tag_insert_errors++;
                            }
                            $tid= intval($db->lastInsertId());
                        }

                        // Create the connection - it is ok to only create the connection for the lowest level category - dont care if it already exists
                        $connect->bindValue(":category_id",$id);
                        $connect->bindValue(":tag_id",$tid);

                        $consuc = $connect->execute();

                        if(!$consuc) {
                            $connection_already_existed++;
                        } else {
                            $connection_created++;
                        }
                    }
                }

                echo "IMPORT REPORT:
                  Kategorie existierend: $category_already_existed, Kategorie neu: $category_didnt_exist, Kategorie Insert Fehler: $category_insert_errors, Kategorie Update Fehler: $category_update_errors,<br/>
                  Taggroup existierend: $taggroup_already_existed, Taggroup neu: $taggroup_didnt_exist, Taggroup Insert Fehler/keine Taggroup angegeben: $taggroup_insert_errors, Taggroup Update Fehler: $taggroup_update_errors<br/>
                  Tag existierend: $tag_already_existed, Tag neu: $tag_didnt_exist, Tag Insert Fehler/kein Tag angegeben: $tag_insert_errors, Tag Update Fehler: $tag_update_errors<br/>
                  Tag/Kategorie Verbindung besteht bereits: $connection_already_existed, Neue Connection: $connection_created
                  ";


            } else {
                array_push($user_messages,array("error","Can only accept .csv-Files."));
            }
        }
    } else {
        array_push($user_messages,array("error","Please choose a file to import."));
    }

    if(empty($user_messages)) {
        array_push($user_messages,array("success","Congrats: Import successfully executed."));
    }
}