<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 1/8/16
 * Time: 4:53 PM
 */
/*
 * Execute an import of google Product List
 */
if(isset($_POST['newimpgpr']) && $_POST['newimpgpr']=="doit") {

    if(isset($_FILES["impfilegpr"])) {

        if ($_FILES["impfilegpr"]["error"] > 0) {
            array_push($user_messages,array("error",$_FILES["file"]["error"]));
        } else {

            if($_FILES["impfilegpr"]["type"]=="text/csv") {

                // file is ok, lets try to parse it and insert it into the database
                $up   = "UPDATE category SET lvl_1 = :lvl1, lvl_2 = :lvl2, lvl_3 = :lvl3, lvl_4 = :lvl4, lvl_5 = :lvl5, lvl_6 = :lvl6, lvl_7 = :lvl7 WHERE gid=:gid";
                $ins  = "INSERT INTO category (gid,lvl_1,lvl_2,lvl_3,lvl_4,lvl_5,lvl_6,lvl_7) VALUES (:gid,:lvl1,:lvl2,:lvl3,:lvl4,:lvl5,:lvl6,:lvl7)";

                $row = 1;
                if (($handle = fopen($_FILES["impfilegpr"]["tmp_name"], "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

                        if($row>0) {

                            $stup =  $db->prepare($up);
                            $stins = $db->prepare($ins);

                            $populate = function($stmt,$data) {
                                $stmt->bindValue(":gid",$data[0]);
                                $stmt->bindValue(":lvl1",$data[1]);
                                $stmt->bindValue(":lvl2",$data[2]);
                                $stmt->bindValue(":lvl3",$data[3]);
                                $stmt->bindValue(":lvl4",$data[4]);
                                $stmt->bindValue(":lvl5",$data[5]);
                                $stmt->bindValue(":lvl6",$data[6]);
                                $stmt->bindValue(":lvl7",$data[7]);
                                return $stmt;
                            };


                            $stup = $populate($stup,$data);
                            $stins = $populate($stins,$data);


                            $stup->execute();

                            if(!($stup->rowCount() ? true : false)) {

                                $stins->execute();

                                if(!($stins->rowCount() ? true : false)) {
                                    array_push($user_messages,array("warning","Row number ".$row." created: SQL Failure: ".$db->errorInfo()[2]));
                                }
                            }
                        }

                        $row++;
                    }
                    fclose($handle);
                } else {
                    array_push($user_messages,array("error","CSV could not be opened."));
                }


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