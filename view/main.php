<?php

if(isset($_REQUEST['edit'])) {
    include("edit.php");
} else if(isset($_REQUEST['export'])) {
    include("export.php");
} else if(isset($_REQUEST['productjson'])) {
    include("productjson.php");
} else if(isset($_REQUEST['updateproduct'])) {
    include("updateproduct.php");
} else {
    include("import-export.php");
}