<?php
include 'dbConexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $senha = password_hash(trim($_POST['senha']), PASSWORD_BCRYPT);

    //verifica se o usuário já existe
    $sqlCheck = $conn->prepare("SELECT usuario FROM usuarios WHERE usuario = ?");
    $sqlCheck->bind_param("s", $usuario);
    $sqlCheck->execute();
    $result = $sqlCheck->get_result();

    if ($result->num_rows > 0) {
        //se  usuário já existir, exibe um alerta e mantém na página de login
        echo "<script>
                alert('Erro: Usuário já cadastrado!');
                window.location.href = 'login.php';
              </script>";
    } else {
        //insere o novo usuário
        $sql = $conn->prepare("INSERT INTO usuarios (usuario, senha) VALUES (?, ?)");
        $sql->bind_param("ss", $usuario, $senha);

        if ($sql->execute()) {
            echo "<script>
                    alert('Usuário cadastrado com sucesso!');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            echo "Erro ao cadastrar: " . $conn->error;
        }

        $sql->close();
    }

    $sqlCheck->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>

<body>
    <h1>Cadastro de Usuário</h1>
    <form method="POST">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required>
        <br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>
        <br>

        <button type="submit">Cadastrar</button>
    </form>

    <footer>
        <p>&copy; 2024 Oficina Mecânica - Qualidade que você pode confiar.</p>
    </footer>

    <script>
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("show");
        }
    </script>
</body>

</html>