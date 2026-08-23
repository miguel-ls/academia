<?php
$conn = new mysqli("172.16.50.23:3306", "root", "1q2w3e4r5t.", "academia_cursos");

if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}

echo "Conectado OK";
?>