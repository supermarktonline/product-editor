<?php

global $db;
global $user_messages;

require("import-scripts/gs1-import.php");
require("import-scripts/fdata-import.php");

/**
 * Execute list name update.
 */
if (isset($_POST["new_import_name"])) {

    $import_id = urldecode($_POST["import_id"]);
    $name = $_POST["new_import_name"];

    $stmt = $db->prepare("UPDATE import SET name = :name WHERE id = :id");
    $stmt->bindValue(":id", $import_id);
    $stmt->bindValue(":name", $name);

    if (!$stmt->execute()) {
        array_push($user_messages, array("error", "Failed to update import."));
    }
}

// get list of imports
$stmt = $db->prepare('SELECT DISTINCT fdata.import_id, nam.name FROM fdata LEFT OUTER JOIN import AS nam ON (nam.id = fdata.import_id) ORDER BY fdata.import_id DESC');
$stmt->execute();
$imports = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<?php include("header.php"); ?>
<body>

<nav class="navbar navbar-default this-navbar">
    <div class="container-fluid">
        <div class="navbar-header"><a class="navbar-brand">Produkteditor</a></div>
    </div>
</nav>


<main id="content">
    <?php
    // Messages Section

    if (!empty($user_messages)) {
        ?>
        <div id="messages">
            <?php
            foreach ($user_messages as $msg) {
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
        <h1>HILFE</h1>
        Unsere aktuelle Hilfeseite findet ihr <a
            href="https://github.com/supermarktonline/product-editor/blob/master/hilfe/README.md">HIER</a>.
    </div>


    <div class="mc admin-area">
        <h1>Import new list</h1>

        <div class="area_sel_container">

            <form method="post" action="" enctype="multipart/form-data">

                <p>Note: List must be a .csv File in the correct format.</p>

                <p><label>Name:</label><input type="text" name="name" value=""/></p>
                <p><input type="file" name="impfile"/></p>
                <p>
                    <button type="submit" name="newimp" value="doit">Import ausführen</button>
                </p>
            </form>

        </div>
    </div>

    <div class="mc" style="display: none">
        <!-- disabled [cl 2016-04-06] -->
        <!-- Not sure how manual additions in database would survive. -->
        <!-- Manual modifications I've made: -->
        <!--
 create function addTags(session integer, segmentI integer, taggroupI integer) returns int
 as $$
 declare
   recR record;
   tagR record;
 begin
   for tagR in select tag.id from tag, taggroup where tag.taggroup = taggroup.id and taggroup.gs1_attribute_type_code = taggroupI loop
     for recR in 
       select gid from category where segment_code = segmentI and gid not in (
         select category_tag.category_id from category_tag where tag_id = tagR.id) loop
       insert into category_tag (category_id, tag_id) values (recR.gid, tagR.id);
     end loop;
   end loop;
   return 1;
 end;
$$ language plpgsql strict;

-- 50000000;"Food/Beverage/Tobacco"
-- 63000000;"Footwear"
-- 71000000;"Sports Equipment"
-- 53000000;"Beauty/Personal Care/Hygiene"
-- 47000000;"Cleaning/Hygiene Products"
-- 10000000;"Pet Care/Food"
-- 54000000;"Baby Care"
-- 67000000;"Clothing"

-- enable lactose free and gluten free for all food items.
-- enable if organic, vegan/vegetarians for some segments (see above)
select addTags(1, 50000000, 20000733); -- lactose free
select addTags(1, 50000000, 20000079); -- gluten free
select addTags(1, 50000000, 20000142); -- if organic
select addTags(1, 50000000, 20000175); -- vegan / vegetarians
select addTags(1, 63000000, 20000142); -- if organic
select addTags(1, 63000000, 20000175); -- vegan / vegetarians
select addTags(1, 71000000, 20000142); -- if organic
select addTags(1, 71000000, 20000175); -- vegan / vegetarians
select addTags(1, 53000000, 20000142); -- if organic
select addTags(1, 53000000, 20000175); -- vegan / vegetarians
select addTags(1, 47000000, 20000142); -- if organic
select addTags(1, 47000000, 20000175); -- vegan / vegetarians
select addTags(1, 10000000, 20000142); -- if organic
select addTags(1, 10000000, 20000175); -- vegan / vegetarians
select addTags(1, 54000000, 20000142); -- if organic
select addTags(1, 54000000, 20000175); -- vegan / vegetarians
select addTags(1, 67000000, 20000142); -- if organic
select addTags(1, 67000000, 20000175); -- vegan / vegetarians
-- enable level of sugar for all Foods:
select addTags(1, 50000000, 20000125); -- level of sugar

-- add PUDDING MIX [30001937] to Baking/Cooking Mixes (Shelf Stable) [10000156]
-- select gid from category where brick_code = 10000156; -- 1338
-- select id from tag where gs1_attribute_value_code = 30001937; -- 7970
insert into category_tag (category_id, tag_id) values (1338, 7970);
          -->
        <h1>GS1 Liste (englisch) importieren oder update (Hinweis: Timeout möglich)</h1>

        <div class="area_sel_container">

            <form method="post" action="" enctype="multipart/form-data">

                <p>Hinweis: Liste muss im .csv format sein</p>

                <p><input type="file" name="impfilegpr"/></p>
                <p>
                    <input type="hidden" name="newimpgpr" value="doit"/>
                    <button type="submit">Import ausführen</button>
                </p>

            </form>

        </div>
    </div>


    <div class="mc">
        <h1>Liste zum Bearbeiten wählen</h1>

        <div class="area_sel_container">
            <?php

            foreach ($imports as $row) {
                ?>
                <div class="area_sel_container_row">
                    <a href="/?edit=<?php echo urlencode($row['import_id']); ?>"><?php echo $row['import_id'] . " (" . $row['name'] . ")"; ?></a>
                    &nbsp;&nbsp;&nbsp;
                <span id="admin_listedit" class="admin-area">
                    <form method="post" action="">
                        Name: <input type="text" name="new_import_name" value="<?php echo $row['name']; ?>"/>
                        <input type="hidden" name="import_id" value="<?php echo urlencode($row['import_id']); ?>"/>
                        <input type="submit" name="update_import" value="Update properties"/>
                    </form>
                </span>
                </div>
                <?php
            }
            ?>
        </div>
    </div>


    <div class="mc admin-area">
        <h1>Select a list to export</h1>
        <div class="area_sel_container">
            <table style="border-collapse: separate; border-spacing: 20px 3px;">
                <?php
                foreach ($imports as $row) {
                    ?>
                    <tr>
                        <td><?php echo $row['import_id']; ?><?php echo ($row['name']) ? "(" . $row["name"] . ")" : ""; ?></td>
                        <td>
                            <div class="area_sel_container_row">
                                <form method="get" action="">
                                    <input type="hidden" name="export"
                                           value="<?php echo urlencode($row['import_id']); ?>"/>
                                    Minstate: <input type="text" size="2" name="minstate" value="10"/>
                                    Maxstate: <input type="text" size="2" name="maxstate" value="10"/>
                                    <input type="submit" value="Export list"/>
                                </form>
                            </div>
                        </td>
                        <td>
                            <form method="get" action="">
                                <input type="hidden" name="export-tags"
                                       value="<?php echo urlencode($row['import_id']); ?>"/>
                                Minstate: <input type="text" size="2" name="minstate" value="10"/>
                                Maxstate: <input type="text" size="2" name="maxstate" value="10"/>
                                <input type="submit" value="Export tags"/>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>

                <tr>
                    <td>ALL</td>
                    <td>
                        <div class="area_sel_container_row">
                            <form method="get" action="">
                                <input type="hidden" name="export" value="ALL"/>
                                Minstate: <input type="text" size="2" name="minstate" value="10"/>
                                Maxstate: <input type="text" size="2" name="maxstate" value="10"/>
                                <input type="submit" value="Export list"/>
                            </form>
                        </div>
                    </td>
                    <td>
                        <form method="get" action="">
                            <input type="hidden" name="export-tags" value="ALL"/>
                            Minstate: <input type="text" size="2" name="minstate" value="10"/>
                            Maxstate: <input type="text" size="2" name="maxstate" value="10"/>
                            <input type="submit" value="Export tags"/>
                        </form>
                    </td>
                </tr>
            </table>

        </div>
    </div>


</main>

<div class="hide">
    <div id="dialog-confirm" title="Delete list permanently?">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>This import will be
            deleted and cannot be recovered. Are you sure you want to delete all items?</p>
    </div>

    <form id="delete_form" method="post" action="">
        <input type="hidden" name="delete_list" value="do"/>
        <input type="hidden" name="todelete" id="todelete" value=""/>
    </form>

</div>
</body>
</html>