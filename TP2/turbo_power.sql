DROP DATABASE IF EXISTS turbo_power;
CREATE DATABASE IF NOT EXISTS turbo_power;
USE turbo_power;

DROP TABLE IF EXISTS clientes;
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(15) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    modelo_carro VARCHAR(100) NOT NULL,
    ano INT NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS ordens_servico;
CREATE TABLE ordens_servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hora_chegada TIME,
    hora_saida TIME,
    modelo VARCHAR(255),
    placa VARCHAR(20),
    ano INT,
    servicos TEXT,
    mecanico VARCHAR(100),
    cliente_id INT, 
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) 
);

DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS produtos;
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    quantidade INT
);

DROP TABLE IF EXISTS produtos_ordem_servico;
CREATE TABLE produtos_ordem_servico (
    ordem_servico_id INT,
    produto_id INT,
    quantidade INT,
    FOREIGN KEY (ordem_servico_id) REFERENCES ordens_servico(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

DROP TABLE IF EXISTS mecanicos;
CREATE TABLE mecanicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

INSERT INTO clientes (nome, telefone, endereco, modelo_carro, ano) VALUES 
('João Silva', '99999-1234', 'Rua A, 123', 'Honda Civic', 2020),
('Maria Oliveira', '98888-5678', 'Avenida B, 456', 'Toyota Corolla', 2019),
('Carlos Souza', '97777-9876', 'Rua C, 789', 'Ford Fiesta', 2018),
('Ana Lima', '96666-5432', 'Rua D, 101', 'Chevrolet Onix', 2021),
('Lucas Martins', '95555-3210', 'Rua E, 102', 'Fiat Argo', 2022);

INSERT INTO mecanicos (nome) VALUES
('Carlos'),
('José'),
('Márcio'),
('Roberto'),
('Felipe');

INSERT INTO ordens_servico (hora_chegada, hora_saida, modelo, placa, ano, servicos, mecanico, cliente_id) VALUES 
('08:00:00', '10:30:00', 'Honda Civic', 'ABC-1234', 2020, 'Troca de óleo e alinhamento', 'Carlos', 1),
('09:00:00', '12:00:00', 'Toyota Corolla', 'XYZ-5678', 2019, 'Revisão completa', 'José', 2),
('10:30:00', '13:00:00', 'Ford Fiesta', 'DEF-1122', 2018, 'Troca de pastilhas de freio', 'Márcio', 3),
('11:00:00', '14:00:00', 'Chevrolet Onix', 'GHI-3344', 2021, 'Troca de filtros e óleo', 'Carlos', 4),
('12:30:00', '15:00:00', 'Fiat Argo', 'JKL-5566', 2022, 'Troca de óleo e alinhamento', 'José', 5);

INSERT INTO produtos (nome, preco, quantidade) VALUES
('Filtro de Ar', 50.00, 100),
('Óleo Lubrificante', 120.00, 75),
('Pastilha de Freio', 80.00, 50),
('Correia Dentada', 150.00, 30),
('Bateria Automotiva', 200.00, 40);

INSERT INTO produtos_ordem_servico (ordem_servico_id, produto_id, quantidade) VALUES 
(1, 2, 2),  
(2, 1, 1), 
(3, 3, 4),
(4, 1, 2),
(5, 4, 1);

