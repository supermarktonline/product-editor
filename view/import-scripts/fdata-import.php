<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 1/8/16
 * Time: 4:54 PM
 */

/**
 * Execute an import of data.
 */
if(isset($_POST['newimp']) && $_POST['newimp']=="doit") {

    if(isset($_FILES["impfile"])) {

        if ($_FILES["impfile"]["error"] > 0) {
            array_push($user_messages,array("error",$_FILES["file"]["error"]));
        } else {

            if($_FILES["impfile"]["type"]=="text/csv") {

                // file is ok, lets try to parse it and insert it into the database
                $pstr = "INSERT INTO fdata VALUES (DEFAULT";
                for($i=0;$i<(NUM_IMPORT_COLS+1);$i++) {
                    $pstr .= ",?";
                }
                for($i=0;$i<NUM_DEFAULT_COLS_AFTER;$i++) {
                    $pstr .= ",DEFAULT";
                }
                $pstr.=")";

                $sqltime = Tool::timePHPtoSQL(time());

                // 1. Create the import
                $stmt = $db->prepare('INSERT INTO import (id,name,media_path) VALUES (:id,:name,:media_path)');
                $stmt->bindValue(":id",$sqltime);
                $stmt->bindValue(":name",$_POST['name']);
                $stmt->bindValue(":media_path",$_POST['media_path']);
                $res = $stmt->execute();

                if($res) {

                    $anySuccess = false;

                    $row = 1;
                    if (($handle = fopen($_FILES["impfile"]["tmp_name"], "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

                            if($row>1) {

                                $stmt =  $db->prepare($pstr);

                                // if not, parsing of the row went probably wrong
                                if(count($data)===NUM_IMPORT_COLS) {

                                    $stmt->bindValue(1,$sqltime);

                                    for($i=2;$i<=NUM_IMPORT_COLS+1;$i++) {
                                        $stmt->bindValue($i,$data[$i-2]);
                                    }

                                    if(!$stmt->execute()) {
                                        array_push($user_messages,array("warning","Row number ".$row." was not imported: SQL Failure: ".$db->errorInfo()[2]));
                                    } else {
                                        $anySuccess = true;
                                    }
                                } else {
                                    array_push($user_messages,array("error","Row number ".$row." was not imported: Incorrect number of fields."));
                                }

                            }

                            $row++;
                        }
                        fclose($handle);

                        // no reason to save empty import
                        if(!$anySuccess) {
                            $stmt = $db->prepare('DELETE FROM import WHERE id=:id)');
                            $stmt->bindValue(":id",$sqltime);
                            $stmt->execute();
                        }

                    } else {
                        array_push($user_messages,array("error","CSV could not be opened."));
                    }

                } else {
                    array_push($user_messages,array("error","Import could not be created."));
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