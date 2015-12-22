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
} else if(isset($_REQUEST['category_connection'])) {
    include("category_connection.php");
} else if(isset($_REQUEST['ingredient_connection'])) {
    include("ingredient_connection.php");
} else if(isset($_REQUEST['ingredient'])) {
    include("ingredient.php");
} else if(isset($_REQUEST['sealetc'])) {
    include("sealetc.php");
} else if(isset($_REQUEST['sealetc_connection'])) {
    include("sealetc_connection.php");
} else if(isset($_REQUEST['category_sealetc_connection'])) {
    include("category_sealetc_connection.php");
} else if(isset($_REQUEST['taggroup'])) {
    include("taggroup.php");
} else {
    include("import-export.php");
} 