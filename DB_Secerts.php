<?php
$username = "student";
$password = "student";
$server = "blitz.cs.niu.edu";
$db = "csci467";

try {
    $dsn = "mysql:host=$server;dbname=$db";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection to database failed: " . $e->getMessage();
}
?>


<?php 
$user = "z1936409";
$pass = "1998Jul20";
$serv = "courses";
$d = "z1936409";
try { // if something goes wrong, an exception is thrown
$sn = "mysql:host=$serv;dbname=$d";
$p = new PDO($sn, $user, $pass);
$p->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}
catch(PDOexception $ex) { // handle that exception
echo "Connection to database failed: " . $ex->getMessage();
}
?>