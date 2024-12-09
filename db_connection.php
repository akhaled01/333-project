<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


function db_connect(): PDO
{
    $host = 'localhost';
    $db_name = 'itcs333';
    $db_user = 'root';
    $db_password = '';
    $db = "mysql:host=$host;dbname=$db_name;charset=utf8mb4";
    $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    return new PDO($db, $db_user, $db_password, $options);
}