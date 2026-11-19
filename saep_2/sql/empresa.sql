DROP DATABASE IF EXISTS empresa;                    -- permite rodar o script de novo
CREATE DATABASE empresa CHARACTER SET utf8mb4;      -- utf8mb4: acentos e emojis
USE empresa;

CREATE TABLE `user` (
    id_user INT          NOT NULL AUTO_INCREMENT,
    nome    VARCHAR(100) NOT NULL,
    CONSTRAINT pk_user PRIMARY KEY (id_user)
) ENGINE = InnoDB;

CREATE TABLE produto (
    id_produto         INT           NOT NULL AUTO_INCREMENT,
    nome               VARCHAR(100)  NOT NULL,
    quantidade_estoque INT           NOT NULL,
    valor_unitario     DECIMAL(10,2) NOT NULL,
    data_cadastro      DATE          NOT NULL,
    CONSTRAINT pk_produto PRIMARY KEY (id_produto)
) ENGINE = InnoDB;

CREATE TABLE movimentacao (
    id_movimentacao       INT                      NOT NULL AUTO_INCREMENT,
    tipo                  ENUM('ENTRADA', 'SAIDA') NOT NULL,
    quantidade            INT                      NOT NULL,
    id_produto            INT                      NOT NULL,
    datetime_movimentacao DATETIME                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_user               INT                      NOT NULL,
    CONSTRAINT pk_movimentacao PRIMARY KEY (id_movimentacao),
    CONSTRAINT fk_movimentacao_produto FOREIGN KEY (id_produto)
        REFERENCES produto (id_produto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_movimentacao_user FOREIGN KEY (id_user)
        REFERENCES `user` (id_user)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB;

INSERT INTO `user` (nome) VALUES
('Administrador');

INSERT INTO produto (nome, quantidade_estoque, valor_unitario, data_cadastro) VALUES
('Amaciante de roupas', 100, 0.50, CURDATE()),
('Detergente', 25, 18.90, CURDATE()),
('Sabão em pó', 40, 22.50, CURDATE());

INSERT INTO movimentacao (tipo, quantidade, id_produto, id_user) VALUES
('ENTRADA', 50, 1, 1),
('SAIDA', 10, 1, 1),
('SAIDA', 5, 2, 1);
