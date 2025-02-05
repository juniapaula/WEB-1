<?php
session_start();
include 'dbConexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //verifica se o formulário foi enviado
    $usuario = trim($_POST['usuario']); //remove espaços em branco do nome de usuário
    $senha = trim($_POST['senha']); //remove espaços em branco da senha

    //previne SQL Injection usando prepared statements
    $sql = $conn->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $sql->bind_param("s", $usuario); //substitui o placeholder "?" pelo nome do usuário
    $sql->execute(); //executa a consulta
    $result = $sql->get_result(); //obtém os resultados da consulta

    if ($result->num_rows > 0) { //verifica se o usuário foi encontrado
        $user = $result->fetch_assoc(); //obtém os dados do usuário
        if (password_verify($senha, $user['senha'])) { //verifica se a senha está correta
            $_SESSION['usuario'] = $user['usuario']; //armazena o usuário na sessão
            header("Location: painel.php"); //redireciona para o painel
            exit();
        } else {
            echo "<script>alert('Senha incorreta!');</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado!');</script>";
    }

    $sql->close(); //fecha o statement
    $conn->close(); //fecha a conexão com o banco de dados
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div class="menu-icon" onclick="toggleMenu()" aria-label="Abrir menu">&#9776;</div>
        <nav id="menu">
            <a href="index.php">Início</a>
            <a href="cadastro_cliente.php">Cadastro de Cliente</a>
            <a href="ordem_servico.php">Ordem de Serviço</a>
            <a href="cadastro_produto.php">Cadastro de Produtos</a>
            <a href="login.php">Login</a>
        </nav>
        <h1>Login</h1>
    </header>
    <main>
        <form method="POST" action="">
            <fieldset>
                <legend>Acesse sua Conta</legend>
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" value="" required>
                <br><br>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" value="" required>
                <br><br>
                <button type="submit">Entrar</button>
            </fieldset>
        </form>

        <form method="POST" action="cadastro_usuario.php">
            <fieldset>
                <legend>Crie sua Conta</legend>
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" value="" required>
                <br><br>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" value="" required>
                <br><br>
                <button type="submit">Cadastrar</button>
            </fieldset>
        </form>

    </main>
    <footer>
        <p>&copy; 2024 Oficina Mecânica - Qualidade que você pode confiar.</p>
    </footer>

    <script>
        //limpa os campos do formulário de login ao carregar a página
        document.addEventListener("DOMContentLoaded", function() {
            const loginForm = document.getElementById("loginForm");
            if (loginForm) {
                loginForm.reset(); // Reseta os campos
            }
        });

        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("show");
        }
    </script>

</body>

</html>