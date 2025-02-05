<?php
$host = 'localhost'; //servidor (XAMPP usa localhost)
$user = 'root';   
$password = 'root'; 
$database = 'turbo_power';

//conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $database);

//verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
