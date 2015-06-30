<?php

if(isset($_REQUEST['edit'])) {
    include("edit.php");
} else if(isset($_REQUEST['export'])) {
    include("export.php");
} else {
    include("import-export.php");
}