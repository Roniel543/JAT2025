<?php

$user = 'root';
$pass = '';
$db = 'jat2025';
$host = 'localhost';

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}