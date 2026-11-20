DROP DATABASE IF EXISTS empresa;
CREATE DATABASE empresa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE empresa;

CREATE TABLE `user` (
    id_user INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('ADMINISTRADOR','OPERADOR') NOT NULL DEFAULT 'OPERADOR',
    ativo TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE produto (
    id_produto INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    quantidade_estoque INTEGER NOT NULL DEFAULT 0,
    valor_unitario NUMERIC(10,2) NOT NULL,
    data_cadastro DATE NOT NULL DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB;

CREATE TABLE movimentacao (
    id_movimentacao INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    tipo VARCHAR(10) NOT NULL CHECK (tipo IN ('ENTRADA', 'SAIDA')),
    quantidade INTEGER NOT NULL,
    id_produto INTEGER NOT NULL,
    datetime_movimentacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_user INTEGER NOT NULL,
    FOREIGN KEY (id_produto) REFERENCES produto(id_produto),
    FOREIGN KEY (id_user) REFERENCES `user`(id_user)
) ENGINE=InnoDB;

INSERT INTO `user` (nome, login, senha, perfil) VALUES
('Administrador', 'admin', '$2y$12$0LcU8nczkpEJzWKvmElBQenufJYY7HnhLpfep9m8ZggU5QVJezohe', 'ADMINISTRADOR');

INSERT INTO produto (nome, quantidade_estoque, valor_unitario) VALUES
('Amaciante de roupas', 100, 0.50),
('Detergente', 25, 18.90),
('Sabão em pó', 40, 22.50);

INSERT INTO movimentacao (tipo, quantidade, id_produto, id_user) VALUES
('ENTRADA', 50, 1, 1),
('SAIDA', 10, 1, 1),
('SAIDA', 5, 2, 1);


UPDATE `user`
SET senha = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    perfil = 'ADMINISTRADOR',
    ativo = 1
WHERE login = 'admin';