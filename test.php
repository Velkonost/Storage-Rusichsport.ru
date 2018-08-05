<?php


error_reporting(E_ALL);

define('HOST', 'localhost');
define('USER', 'root');
define('PASSWORD', 'root');
define('DB', 'storage');
$CONNECT = mysqli_connect(HOST, USER, PASSWORD, DB);
    
echo "123";