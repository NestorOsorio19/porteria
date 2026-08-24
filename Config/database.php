<?php

function connection()
{
    $host = "localhost";
    $dbname = "porteria";
    $user = "root";
    $pass = "";

    try {

        $connection = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass
        );

        $connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        $connection->setAttribute(
            PDO::ATTR_DEFAULT_FETCH_MODE,
            PDO::FETCH_ASSOC
        );

        $connection->setAttribute(
            PDO::ATTR_EMULATE_PREPARES,
            false
        );

        return $connection;

    } catch (PDOException $e) {

        die("Error de conexión: " . $e->getMessage());

    }
}