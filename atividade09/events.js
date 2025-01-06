document.getElementById('btnCadastrar').addEventListener('click', function () {
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const telefone = document.getElementById('telefone').value.trim();
    const idade = document.getElementById('idade').value.trim();
    const endereco = document.getElementById('endereco').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const telefoneRegex = /^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/;

    if (!nome || !email || !telefone || !idade || !endereco) {
        alert('Por favor, preencha todos os campos antes de cadastrar.');
        return;
    }
    if (!emailRegex.test(email)) {
        alert('Por favor, insira um email válido.');
        return;
    }
    if (!telefoneRegex.test(telefone)) {
        alert('Por favor, insira um telefone válido (exemplo: (11) 99999-9999).');
        return;
    }
    const tabela = document.getElementById('tabelaUsuarios').querySelector('tbody');
    const novaLinha = document.createElement('tr');

    novaLinha.innerHTML = `
        <td>${nome}</td>
        <td>${email}</td>
        <td>${telefone}</td>
        <td>${idade}</td>
        <td>${endereco}</td>
        <td><button class="delete-btn">Excluir</button></td>
    `;
    tabela.appendChild(novaLinha);
    document.getElementById('cadastroForm').reset();
    novaLinha.querySelector('.delete-btn').addEventListener('click', function () {
        tabela.removeChild(novaLinha);
    });
});
