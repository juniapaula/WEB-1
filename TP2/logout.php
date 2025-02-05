<?php
session_start();
session_destroy(); //destroi todas as variáveis de sessão e encerra a sessão
//redireciona o usuário para a página de login após o logout
header("Location: login.php");
//interrompe a execução do script após o redirecionamento
exit();
?>
