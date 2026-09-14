CREATE DATABASE IF NOT EXISTS sistema_crud2
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sistema_crud2;

CREATE TABLE IF NOT EXISTS transacoes (
    id INT NOT NULL AUTO_INCREMENT,
    tipo ENUM('receita', 'despesa') NOT NULL,
    valor DECIMAL(12,2) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    data DATE NOT NULL,
    PRIMARY KEY (id),
    CHECK (valor > 0)
);

CREATE TABLE IF NOT EXISTS metas (
    id INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    valor_objetivo DECIMAL(12,2) NOT NULL,
    valor_atual DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    data_limite DATE NOT NULL,
    PRIMARY KEY (id),
    CHECK (valor_objetivo > 0),
    CHECK (valor_atual >= 0),
    CHECK (valor_atual <= valor_objetivo)
);

CREATE TABLE IF NOT EXISTS diario (
    id INT NOT NULL AUTO_INCREMENT,
    valor_diario DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (id),
    CHECK (valor_diario >= 0)
);

INSERT INTO diario (id, valor_diario)
VALUES (1, 0.00)
ON DUPLICATE KEY UPDATE id = id;