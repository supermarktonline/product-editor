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
                    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {




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
                        $findid = $db->prepare("SELECT gid FROM category WHERE segment_code=:segment_code AND family_code=:family_code AND class_code=:class_code AND brick_code=:brick_code");
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
                                $up = $db->prepare("UPDATE category SET segment_description_en=:segment_description_en, family_description_en=:family_description_en, class_description_en=:class_description_en, brick_description_en=:brick_description_en WHERE gid=:id");
                                $up->bindValue(":segment_description_en",$segment_description_en);
                                $up->bindValue(":family_description_en",$family_description_en);
                                $up->bindValue(":class_description_en",$class_description_en);
                                $up->bindValue(":brick_description_en",$brick_description_en);
                                $up->bindValue(":id",$id);

                                $upsuc = $up->execute();

                                if(!$upsuc) {
                                    $category_update_errors++;
                                }
                            }
                        } else {
                            $category_didnt_exist++;

                            $in = $db->prepare("INSERT INTO category(segment_code,family_code,class_code,brick_code,segment_description_en,family_description_en,class_description_en,brick_description_en)
                              VALUES (:segment_code,:family_code,:class_code,:brick_code,:segment_description_en,:family_description_en,:class_description_en,:brick_description_en)");
                            $in->bindValue(":segment_code",$segment_code);
                            $in->bindValue(":family_code",$family_code);
                            $in->bindValue(":class_code",$class_code);
                            $in->bindValue(":brick_code",$brick_code);
                            $in->bindValue(":segment_description_en",$segment_description_en);
                            $in->bindValue(":family_description_en",$family_description_en);
                            $in->bindValue(":class_description_en",$class_description_en);
                            $in->bindValue(":brick_description_en",$brick_description_en);

                            $insuc = $in->execute();

                            if(!$insuc) {
                                $category_insert_errors++;
                            }
                            $id= intval($db->lastInsertId());
                        }


                        // if not exists insert taggroup, otherwise get its id
                        $tgid = $db->prepare("SELECT id FROM taggroup WHERE gs1_attribute_type_code=:gs1_attribute_type_code");
                        $tgid->bindValue(":gs1_attribute_type_code",$tg_gs1_attribute_type_code);
                        $tgid->execute();

                        $tgid = intval($tgid->fetchColumn(0));

                        if($tgid>0) {
                            $taggroup_already_existed++;
                            if($updates) {
                                $up = $db->prepare("UPDATE taggroup SET muid=:muid WHERE id=:id");
                                $up->bindValue(":muid",$tg_muid);
                                $up->bindValue(":id",$tgid);

                                $upsuc = $up->execute();

                                if(!$upsuc) {
                                    $taggroup_update_errors++;
                                }
                            }
                        } else {
                            $taggroup_didnt_exist++;
                            $in = $db->prepare("INSERT INTO taggroup(gs1_attribute_type_code,muid) VALUES (:gs1_attribute_type_code,:muid)");
                            $in->bindValue(":gs1_attribute_type_code",$tg_gs1_attribute_type_code);
                            $in->bindValue(":muid",$tg_muid);

                            $insuc = $in->execute();

                            if(!$insuc) {
                                $taggroup_insert_errors++;
                            }
                            $tgid= intval($db->lastInsertId());
                        }

                        // if not exists insert tag, otherwise get its id
                        $tid = $db->prepare("SELECT id FROM tag WHERE gs1_attribute_value_code=:gs1_attribute_value_code AND taggroup=:taggroup");
                        $tid->bindValue(":gs1_attribute_value_code",$t_gs1_attribute_value_code);
                        $tid->bindValue(":taggroup",$tgid);
                        $tid->execute();

                        $tid = intval($tid->fetchColumn(0));

                        if($tid>0) {
                            $tag_already_existed++;
                            if($updates) {
                                $up = $db->prepare("UPDATE tag SET muid=:muid WHERE id=:id");
                                $up->bindValue(":muid",$t_muid);
                                $up->bindValue(":id",$tid);

                                $upsuc = $up->execute();

                                if(!$upsuc) {
                                    $tag_update_errors++;
                                }
                            }
                        } else {
                            $tag_didnt_exist++;
                            $in = $db->prepare("INSERT INTO tag(gs1_attribute_value_code,taggroup,muid,type) VALUES (:gs1_attribute_value_code,:taggroup,:muid,:type)");
                            $in->bindValue(":gs1_attribute_value_code",$t_gs1_attribute_value_code);
                            $in->bindValue(":taggroup",$tgid);
                            $in->bindValue(":muid",$t_muid);
                            $in->bindValue(":type",NULL);

                            $insuc = $in->execute();

                            if(!$insuc) {
                                $tag_insert_errors++;
                            }
                            $tid= intval($db->lastInsertId());
                        }

                        // Create the connection - it is ok to only create the connection for the lowest level category - dont care if it already exists
                        $connect = $db->prepare("INSERT INTO category_tag (category_id,tag_id) VALUES (:category_id,:tag_id)");
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