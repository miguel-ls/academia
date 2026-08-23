<?php

$password = "admin"; // nueva contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

echo $hash;