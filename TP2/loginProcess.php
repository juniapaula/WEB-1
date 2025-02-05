<?php
session_start();
include 'dbConexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //verifica se a requisição foi feita via método POST
    $usuario = $_POST['username']; //obtém o valor do campo 'username' do formulário
    $senha = $_POST['password']; //obtém o valor do campo 'password' do formulário

    //verifica se o usuário existe no banco de dados
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
    $result = $conn->query($sql); //executa a consulta SQL no banco de dados

    if ($result->num_rows > 0) { //se o resultado da consulta retornar uma linha (usuário encontrado)
        $row = $result->fetch_assoc(); //obtém os dados do usuário encontrado na consulta
        
        if (password_verify($senha, $row['senha'])) { //verifica se a senha informada no formulário corresponde à senha armazenada no banco de dados
            $_SESSION['usuario'] = $usuario; //se senha correta, inicia a sessão e armazena o nome do usuário na variável de sessão
            header("Location: painel.php"); //redireciona o usuário para a página 'painel.php'
            exit(); //interrompe a execução do código após o redirecionamento
        } else {
            //se senha incorreta, exibe um alerta e redireciona para a página de login
            echo "<script>alert('Senha incorreta!'); window.location.href = 'login.php';</script>";
        }
    } else {
        //se usuário não encontrado, exibe um alerta e redireciona para a página de login
        echo "<script>alert('Usuário não encontrado!'); window.location.href = 'login.php';</script>";
    }
}
?>
