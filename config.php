<?php

/* Database connection */
define('DB_HOST','psql1'); // localhost
define('DB_NAME','postgres'); // db_david_product_editor
define('DB_CHARSET','utf8');
define('DB_USER','postgres');
define('DB_PASSWORD','');
define('DB_PORT','5432');

define('NUM_COLS_BEFORE',2);
define('NUM_IMPORT_COLS',77);
define('NUM_DEFAULT_COLS_AFTER',44);
define('NUM_COLS_OVERALL',NUM_COLS_BEFORE+NUM_DEFAULT_COLS_AFTER+NUM_IMPORT_COLS);

define('NUM_GOOGLE_IMPORT_COLS',8);

define('VIEWPATH','view/');

define('APP_URL',((strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,4))=='http') ? "http://" : "https://").$_SERVER['HTTP_HOST']."/");
