<?php
session_start();
$usuarioLogado = isset($_SESSION['usuario']); //verifica se o usuário está logado
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turbo Power</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="fonte.css">
</head>
<body>
    <header>
        <div class="menu-icon" onclick="toggleMenu()" aria-label="Abrir menu">&#9776;</div>
        <nav id="menu">
            <a href="index.php">Início</a>
            <a href="cadastro_cliente.php">Cadastro de Cliente</a>
            <a href="ordem_servico.php">Ordem de Serviço</a> 
            <a href="cadastro_produto.php">Cadastro de Produtos</a>
            <a href="#" id="loginLink" onclick="toggleLogin()">
                <?php echo $usuarioLogado ? 'Sair' : 'Login'; ?>
            </a>
        </nav>
        <h1>Turbo Power</h1>
    </header>
    <main>
        <section>
            <h2>Sobre Nós</h2>
            <p>
                Nossa oficina está comprometida em oferecer serviços de qualidade e confiança. Nossa equipe experiente e 
                apaixonada por cuidar do seu veículo utiliza os mais modernos equipamentos e técnicas para garantir a sua 
                segurança e conforto. Nosso objetivo é proporcionar uma experiência única, com atendimento personalizado, 
                transparência e agilidade em todos os serviços. Conte conosco para manter seu carro sempre em perfeitas 
                condições!
            </p>
        </section>
        <section>
            <h2>Serviços Ofertados</h2>
            <ul>
                <li><strong>Troca de óleo:</strong> Produtos de alta qualidade, adaptados ao seu veículo.</li>
                <li><strong>Revisão completa:</strong> Check-up de todos os sistemas do seu carro, incluindo freios, suspensão e filtros.</li>
                <li><strong>Balanceamento e alinhamento:</strong> Garantindo maior estabilidade e durabilidade dos pneus.</li>
                <li><strong>Diagnóstico eletrônico:</strong> Identificação e solução rápida de problemas no veículo.</li>
            </ul>
        </section>
        <section>
            <h2>Produtos</h2>
            <p>Trabalhamos com peças originais e de alta qualidade para veículos nacionais e importados.</p>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Oficina Mecânica - Qualidade que você pode confiar.</p>
    </footer>

    <script>
        //verifica se o usuário está logado via PHP e define a variável em JavaScript
        let usuarioLogado = <?php echo $usuarioLogado ? 'true' : 'false'; ?>;

        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("show");
        }

        function toggleLogin() {
            if (usuarioLogado) {
                //redireciona para logout.php para encerrar a sessão
                window.location.href = 'logout.php';
            } else {
                //redireciona para login.php
                window.location.href = 'login.php';
            }
        }
    </script>
</body>
</html>
