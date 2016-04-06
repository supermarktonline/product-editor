<?php

/* Database connection */
define('DB_HOST',''); // localhost
define('DB_NAME',''); // db_david_product_editor
define('DB_CHARSET','utf8');
define('DB_USER','');
define('DB_PASSWORD','');
define('DB_PORT','');

define('NUM_COLS_BEFORE',2);
define('NUM_IMPORT_COLS',77);
define('NUM_DEFAULT_COLS_AFTER',51);
define('NUM_COLS_OVERALL',NUM_COLS_BEFORE+NUM_DEFAULT_COLS_AFTER+NUM_IMPORT_COLS);

define('NUM_GOOGLE_IMPORT_COLS',8);

define('VIEWPATH','view/');

define('APP_URL',((strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,4))=='http') ? "http://" : "https://").$_SERVER['HTTP_HOST']."/");

define('NUMERICAL_VALUE_TYPES_MAP',serialize(
    array(
        'numeric'=> array('en'=>'','de'=>''),
        'percent'=> array('en'=>'%','de'=>'%'),
        'kilogram'=> array('en'=>'kg','de'=>'kg'),
        'gram'=> array('en'=>'g','de'=>'g'),
        'milligram'=> array('en'=>'mg','de'=>'mg'),
        'liter'=> array('en'=>'l','de'=>'l'),
        'milliliter'=> array('en'=>'l','de'=>'l'),
        'seconds'=> array('en'=>'s','de'=>'s'),
        'minutes'=> array('en'=>'m','de'=>'m'),
        'hours'=> array('en'=>'h','de'=>'Stunden'),
        'days'=> array('en'=>'d','de'=>'Tage'),
        'permill'=> array('en'=>'‰','de'=>'‰'),
        'squaremeters'=> array('en'=>'m²','de'=>'m²'),
        'cubicmeters'=> array('en'=>'m³','de'=>'m³')
    )
));

define('HTTP_USERNAME', 'username');
define('HTTP_PASSWORD', 'password');
