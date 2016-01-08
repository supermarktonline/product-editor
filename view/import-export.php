<?php

global $db;
global $user_messages;

require("import-scripts/gs1-import.php");
require("import-scripts/fdata-import.php");

/**
 * Execute list name update.
 */
if(isset($_POST["new_import_name"])) {
    
    $import_id = urldecode($_POST["import_id"]);
    $name = $_POST["new_import_name"];
    $media_path = $_POST["media_path"];
    
    $stmt =  $db->prepare("UPDATE import SET name = :name,media_path = :media_path WHERE id = :id");
    $stmt->bindValue(":id",$import_id);
    $stmt->bindValue(":media_path",$media_path);
    $stmt->bindValue(":name",$name);
    
    if(!$stmt->execute()) {
        array_push($user_messages,array("error","Failed to update import."));
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
        
        $stmt2 =  $db->prepare("DELETE FROM import WHERE id = :id");
        $stmt2->bindValue(":id",urldecode($_POST["todelete"]));
        $stmt2->execute();
        
    }
}


// get list of imports
$stmt = $db->prepare('SELECT DISTINCT fdata.import_id, nam.name,nam.media_path FROM fdata LEFT OUTER JOIN import AS nam ON (nam.id = fdata.import_id) ORDER BY fdata.import_id DESC');
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
        <h1>GS1 Liste (englisch) importieren oder update (Hinweis: Timeout möglich)</h1>
        
        <div class="area_sel_container">
        
            <form method="post" action="" enctype="multipart/form-data">
                
                <p>Hinweis: Liste muss im .csv format sein</p>
                
                <p><input type="file" name="impfilegpr" /></p>
                <p>
                    <input type="hidden"  name="newimpgpr" value="doit" />
                    <button type="submit">Import ausführen</button>
                </p>
                
            </form>
            
        </div>
      </div>
      
      

      <div class="mc">
        <h1>Liste zum bearbeiten wählen</h1>
        
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
                    <input type="submit" name="update_import" value="Update properties" />
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
        <p>Note: For products with state "10" the state is updated to "15", no change otherwise.</p>
        <div class="area_sel_container">
        <?php
        foreach ($imports as $row) {
            ?>
            <div class="area_sel_container_row">
                <form method="get" action="">
                    <?php echo $row['import_id']; ?> <?php echo ($row['name']) ? "(".$row["name"].")" : ""; ?>
                    <input type="hidden" name="export" value="<?php echo urlencode($row['import_id']); ?>" />
                    | Minstate: <input type="text" size="2" name="minstate" value="10" />
                    Maxstate: <input type="text" size="2" name="maxstate" value="10" />
                    <input type="submit" value="Export list" />
                </form>
                &nbsp;&nbsp;&nbsp;|||&nbsp;&nbsp;&nbsp;
                <form method="get" action="">
                    <input type="hidden" name="export-tags" value="<?php echo urlencode($row['import_id']); ?>" />
                    Minstate: <input type="text" size="2" name="minstate" value="10" />
                    Maxstate: <input type="text" size="2" name="maxstate" value="10" />
                    <input type="submit" value="Export tags" />
                </form>
            </div>
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