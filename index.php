<?php


/*
 * Required functions:
 * 
 * 1. Import für Liste ohne die entsprechenden Angaben
 * 
 */


require('config-live.php');
require('model/Tool.php');
require('dal/db.php');

global $db;
$dbobj = new DB();
$db = $dbobj->getDB();

global $user_messages;
$user_messages = array();

require('view/main.php');