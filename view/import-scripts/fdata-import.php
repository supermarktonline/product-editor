<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 1/8/16
 * Time: 4:54 PM
 */

$pStr = "INSERT INTO public.fdata(import_id, bestandsfuehrer, \"productImages\") VALUES (:iid, :inv, :img);";

/**
 * Execute an import of data.
 */
if (isset($_POST['newimp']) && $_POST['newimp'] == "doit") {

    if (isset($_FILES["impfile"])) {

        if ($_FILES["impfile"]["error"] > 0) {
            array_push($user_messages, array("error", $_FILES["file"]["error"]));
        } else {

            if ($_FILES["impfile"]["type"] == "text/csv") {

                $importId = Tool::timePHPtoSQL(time());

                // 1. Create the import
                $stmt = $db->prepare('INSERT INTO import (id,name) VALUES (:id,:name)');
                $stmt->bindValue(":id", $importId);
                $stmt->bindValue(":name", $_POST['name']);
                $res = $stmt->execute();

                if ($res) {

                    $anySuccess = false;

                    if (($handle = fopen($_FILES["impfile"]["tmp_name"], "r")) !== FALSE) {
                        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

                            // if not, parsing of the row went probably wrong
                            if (count($data) === 2) {
                                $stmt = $db->prepare($pStr);

                                $stmt->bindValue(":iid", $importId);
                                $stmt->bindValue(":inv", $data[0]);
                                $stmt->bindValue(":img", $data[1]);

                                if ($stmt->execute()) {
                                    $anySuccess = true;
                                } else {
                                    array_push($user_messages, array("warning", "Row was not imported: SQL Failure: " . $db->errorInfo()[2]));
                                }
                            } else {
                                array_push($user_messages, array("error", "Row was not imported: Incorrect number of fields."));
                            }
                        }
                        fclose($handle);

                        // no reason to save empty import
                        if (!$anySuccess) {
                            $stmt = $db->prepare('DELETE FROM import WHERE id=:id)');
                            $stmt->bindValue(":id", $importId);
                            $stmt->execute();
                        }

                    } else {
                        array_push($user_messages, array("error", "CSV could not be opened."));
                    }

                } else {
                    array_push($user_messages, array("error", "Import could not be created."));
                }


            } else {
                array_push($user_messages, array("error", "Can only accept .csv-Files."));
            }
        }
    } else {
        array_push($user_messages, array("error", "Please choose a file to import."));
    }

    if (empty($user_messages)) {
        array_push($user_messages, array("success", "Congrats: Import successfully executed."));
    }
}