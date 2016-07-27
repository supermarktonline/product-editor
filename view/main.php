<?php

if(isset($_REQUEST['edit'])) {
    include("edit.php");
} else if(isset($_REQUEST['export'])) {
    include("export.php");
} else if(isset($_REQUEST['export-tags'])) {
    include("export-tags.php");
} else if(isset($_REQUEST['productjson'])) {
    include("productjson.php");
} else if(isset($_REQUEST['updateproduct'])) {
    include("updateproduct.php");
} else if(isset($_REQUEST['ingredient_connection'])) {
    include("ingredient_connection.php");
} else if(isset($_REQUEST['ingredient'])) {
    include("ingredient.php");
} else if(isset($_REQUEST['tag'])) {
    include("tag.php");
} else if(isset($_REQUEST['tag_connection'])) {
    include("tag_connection.php");
} else if(isset($_REQUEST['category_tag_connection'])) {
    include("category_tag_connection.php");
} else if(isset($_REQUEST['taggroup'])) {
    include("taggroup.php");
} else if(isset($_REQUEST['action']) && $_REQUEST['action']=="bricktree") {
    include("bricktree.php");
} else if(isset($_REQUEST['reserve'])) {
    include("reserve.php");
} else {
    include("import-export.php");
} 