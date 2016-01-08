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

                $already_existed = 0;
                $didnt_exist = 0;
                $errors = 0;
                $tgerrors = 0;
                $terrors = 0;
                $conerrors = 0;


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
                            $already_existed++;

                            $up = $db->prepare("UPDATE category SET segment_description_en=:segment_description_en, family_description_en=:family_description_en, class_description_en=:class_description_en, brick_description_en=:brick_description_en WHERE gid=:id");
                            $up->bindValue(":segment_description_en",$segment_description_en);
                            $up->bindValue(":family_description_en",$family_description_en);
                            $up->bindValue(":class_description_en",$class_description_en);
                            $up->bindValue(":brick_description_en",$brick_description_en);
                            $up->bindValue(":id",$id);

                            $upsuc = $up->execute();

                            if(!$upsuc) {
                                $errors++;
                            }
                        } else {
                            $didnt_exist++;

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
                                $errors++;
                            }
                            $id= intval($db->lastInsertId());
                        }


                        // if not exists insert taggroup, otherwise get its id
                        $tgid = $db->prepare("SELECT id FROM taggroup WHERE gs1_attribute_type_code=:gs1_attribute_type_code");
                        $tgid->bindValue(":gs1_attribute_type_code",$tg_gs1_attribute_type_code);
                        $tgid->execute();

                        $tgid = intval($tgid->fetchColumn(0));

                        if($tgid>0) {
                            $up = $db->prepare("UPDATE taggroup SET muid=:muid WHERE id=:id");
                            $up->bindValue(":muid",$tg_muid);
                            $up->bindValue(":id",$tgid);

                            $upsuc = $up->execute();

                            if(!$upsuc) {
                                $tgerrors++;
                            }
                        } else {
                            $in = $db->prepare("INSERT INTO taggroup(gs1_attribute_type_code,muid) VALUES (:gs1_attribute_type_code,:muid)");
                            $in->bindValue(":gs1_attribute_type_code",$tg_gs1_attribute_type_code);
                            $in->bindValue(":muid",$tg_muid);

                            $insuc = $in->execute();

                            if(!$insuc) {
                                $tgerrors++;
                            }
                            $tgid= intval($db->lastInsertId());
                        }

                        // if not exists insert tag, otherwise get its id
                        $tid = $db->prepare("SELECT id FROM tag WHERE gs1_attribute_value_code=:gs1_attribute_value_code");
                        $tid->bindValue(":gs1_attribute_value_code",$t_gs1_attribute_value_code);
                        $tid->execute();

                        $tid = intval($tid->fetchColumn(0));

                        if($tid>0) {
                            $up = $db->prepare("UPDATE tag SET muid=:muid WHERE id=:id");
                            $up->bindValue(":muid",$t_muid);
                            $up->bindValue(":id",$tgid);

                            $upsuc = $up->execute();

                            if(!$upsuc) {
                                $terrors++;
                            }
                        } else {
                            $in = $db->prepare("INSERT INTO tag(gs1_attribute_value_code,taggroup,muid,type) VALUES (:gs1_attribute_value_code,:taggroup,:muid,:type)");
                            $in->bindValue(":gs1_attribute_value_code",$t_gs1_attribute_value_code);
                            $in->bindValue(":taggroup",$tgid);
                            $in->bindValue(":muid",$t_muid);
                            $in->bindValue(":type",NULL);

                            $insuc = $in->execute();

                            if(!$insuc) {
                                $terrors++;
                            }
                            $tid= intval($db->lastInsertId());
                        }

                        // Create the connection - it is ok to only create the connection for the lowest level category - dont care if it already exists
                        $connect = $db->prepare("INSERT INTO category_tag (category_id,tag_id) VALUES (:category_id,:tag_id)");
                        $connect->bindValue(":category_id",$id);
                        $connect->bindValue(":tag_id",$tid);

                        $consuc = $connect->execute();

                        if(!$consuc) {
                            $conerrors++;
                        }
                    }
                }

                $already_existed = 0;
                $didnt_exist = 0;
                $errors = 0;
                $tgerrors = 0;
                $terrors = 0;
                $conerrors = 0;
                array_push($user_messages,"success","IMPORT REPORT: Bereits existierend: $already_existed, Neu: $didnt_exist, Kategorie Upsert: $errors Fehler, Taggroup Upsert: $tgerrors Fehler, Tag Upsert: $terrors Fehler, Tag/Kategorie Insert: $conerrors (Fehler oder besteht bereits");


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