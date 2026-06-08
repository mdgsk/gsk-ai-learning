<?php

$pdo = new PDO(
    "mysql:host=HOST;dbname=DB_NAME",
    "USERNAME",
    "PASSWORD"
);

$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);