<?php

require_once 'env.php';

try {
    $db = new PDO("mysql:host=" . $_ENV["DB_URL"] . ";dbname=" . $_ENV["DB_NAME"] . ";charset=utf8mb4", $_ENV["DB_USER"], $_ENV["DB_PWD"]);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    die("DB Connect Error : " . $ex->getMessage());
}
