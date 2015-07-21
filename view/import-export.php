<?php

global $db;
global $user_messages;


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
                $edited=false;
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
                    
                    // also save the name mapping
                    if($anySuccess) {
                        $stmt = $db->prepare('INSERT INTO import_properties (import_id,name,media_path) VALUES (:import_id,:name,:media_path)');
                        $stmt->bindValue(":import_id",$sqltime);
                        $stmt->bindValue(":name",$_POST['name']);
                        $stmt->bindValue(":media_path",$_POST['media_path']);
                        $stmt->execute();
                    }
                    
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
                            
                           

                            if(!$stup->execute()) {
                                if(!$stins->execute()) {
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


/**
 * Execute list name update.
 */
if(isset($_POST["new_import_name"])) {
    
    $import_id = urldecode($_POST["import_id"]);
    $name = $_POST["new_import_name"];
    $media_path = $_POST["media_path"];
    
    $stmt =  $db->prepare("UPDATE import_properties SET name = :name,media_path = :media_path WHERE import_id = :import_id");
    $stmt->bindValue(":import_id",$import_id);
    $stmt->bindValue(":media_path",$media_path);
    $stmt->bindValue(":name",$name);
    
    if(!$stmt->execute() || $stmt->rowCount() < 1) {
        $stmt = $db->prepare('INSERT INTO import_properties (import_id,name,media_path) VALUES (:import_id,:name,:media_path)');
        $stmt->bindValue(":import_id",$import_id);
        $stmt->bindValue(":name",$name);
        $stmt->bindValue(":media_path",$media_path);
        $stmt->execute();
    } 
}



/**
 * Execute list deletion.
 */
if(isset($_POST["delete_list"]) && $_POST["delete_list"]=="do") {
    if(isset($_POST["todelete"]) && strlen($_POST["todelete"]>1)) {
        $stmt =  $db->prepare("DELETE FROM fdata WHERE import_id = :import_id");
        $stmt->bindValue(":import_id",urldecode($_POST["todelete"]));
        
        if(!$stmt->execute()) {
            array_push($user_messages,array("error","Import could not be deleted: SQL Failure: ".$db->errorInfo()[2]));
        } else {
            array_push($user_messages,array("success","The import ".urldecode($_POST["todelete"])." was successfully deleted."));
        }
        
        $stmt2 =  $db->prepare("DELETE FROM import_properties WHERE import_id = :import_id");
        $stmt2->bindValue(":import_id",urldecode($_POST["todelete"]));
        $stmt2->execute();
        
    }
}


// get list of imports
$stmt = $db->prepare('SELECT DISTINCT fdata.import_id, nam.name,nam.media_path FROM fdata LEFT OUTER JOIN import_properties AS nam ON (nam.import_id = fdata.import_id) ORDER BY fdata.import_id DESC');
$stmt->execute();
$imports = $stmt->fetchAll();
        
?>
<!DOCTYPE html>
<html>
  <?php include ("header.php"); ?>
  <body>
      
    <nav class="navbar navbar-default this-navbar">
      <div class="container-fluid">
        <div class="navbar-header"><a class="navbar-brand">Produkteditor</a></div>
      </div>
    </nav>
      
      
      <main id="content">
      <?php
      // Messages Section
      
      if(!empty($user_messages)) {
      ?>
      <div id="messages">
          <?php
          foreach($user_messages as $msg) {
          ?>
            <div class="umsg <?php echo $msg[0]; ?>">
                <?php echo $msg[1]; ?>
            </div>
          <?php
          }
          ?>
      </div>
      <?php
      } ?>
      
          
          
      <div class="mc">
        <h1>Import new list</h1>
        
        <div class="area_sel_container">
        
            <form method="post" action="" enctype="multipart/form-data">
                
                <p>Note: List must be a .csv File in the correct format.</p>
                
                <p><label>Name:</label><input type="text" name="name" value="" /></p>
                <p><label>Media Path:</label><input type="text" name="media_path" value="" /></p>
                <p><input type="file" name="impfile" /></p>
                <p><button type="submit" name="newimp" value="doit">Import ausführen</button></p>
            </form>
            
        </div>
      </div>
          
      <div class="mc">
        <h1>Import or update Google product category list</h1>
        
        <div class="area_sel_container">
        
            <form method="post" action="" enctype="multipart/form-data">
                
                <p>Note: List must be a .csv File in the correct format.</p>
                
                <p><input type="file" name="impfilegpr" /></p>
                <p><button type="submit" name="newimpgpr" value="doit">Import ausführen</button></p>
                
            </form>
            
        </div>
      </div>
      
      

      <div class="mc">
        <h1>Select a list to edit</h1>
        
        <div class="area_sel_container">
        <?php
        
        foreach ($imports as $row) {
            ?>
            <div class="area_sel_container_row">
                <a href="/?edit=<?php echo urlencode($row['import_id']); ?>"><?php echo $row['import_id']; ?></a> 
                &nbsp;&nbsp;&nbsp;
                <form method="post" action="">
                    Name: <input type="text" name="new_import_name" value="<?php echo $row['name']; ?>" />
                    Media Path: <input type="text" name="media_path" value="<?php echo $row['media_path']; ?>" />
                    <input type="hidden" name="import_id" value="<?php echo urlencode($row['import_id']); ?>" />
                    <input type="submit" name="update_import_properties" value="Update properties" />
                </form>
                &nbsp;&nbsp;&nbsp;
                <a href="#" data-deletelist="<?php echo urlencode($row['import_id']); ?>">[Delete this import]</a>
            </div>
            <?php
        }
        ?>
        </div>
      </div>
          
    
      <div class="mc">
        <h1>Select a list to export</h1>
        
        <div class="area_sel_container">
        <?php
        foreach ($imports as $row) {
            ?>
            <a href="/?export=<?php echo urlencode($row['import_id']); ?>">
                <?php echo $row['import_id']; ?> <?php echo ($row['name']) ? "(".$row["name"].")" : ""; ?>
            </a>
            <br/>
            <?php
        }
        ?>
        </div>
      </div>
    

    </main>

    <div class="hide">
        <div id="dialog-confirm" title="Delete list permanently?">
          <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This import will be deleted and cannot be recovered. Are you sure you want to delete all items?</p>
        </div>
        
        <form id="delete_form" method="post" action="">
            <input type="hidden" name="delete_list" value="do" />
            <input type="hidden" name="todelete" id="todelete" value="" />
        </form>
        
    </div>
      
      <script type="text/javascript">
       
       $(document).on('click','[data-deletelist]',function() {
           confirmDelete($(this).attr("data-deletelist"));
       });
       
        function confirmDelete(listId) {
          $( "#dialog-confirm" ).dialog({
            resizable: false,
            height:180,
            modal: true,
            buttons: {
              "Delete all items": function() {
                $("#todelete").val(listId);
                $("#delete_form").submit();
                $( this ).dialog( "close" );
              },
              Cancel: function() {
                $( this ).dialog( "close" );
              }
            }
          });
      }
      </script>
      
  </body>
</html>