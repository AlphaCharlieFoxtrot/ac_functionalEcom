<?php
$host = "127.0.0.1";
$dbname = 'ecommerce';
$username = 'root';
$password = '';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (Exception $e){
    die('Connexion échoué : ' . $e->getMessage());
}
