<?php

declare(strict_types=1);

class Carteira
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSaldo(): float
    {
        $sql = "SELECT 
                    COALESCE(
                        SUM(
                            CASE
                                WHEN tipo = 'receita' THEN valor
                                WHEN tipo = 'despesa' THEN -valor
                                ELSE 0
                            END
                        ),
                        0
                    ) AS saldo
                FROM transacoes";

        $stmt = $this->pdo->query($sql);

        return (float) $stmt->fetchColumn();
    }

    public function getHistorico(): array
    {
        $sql = "SELECT id, tipo, valor, descricao, data
                FROM transacoes
                ORDER BY data DESC, id DESC";

        $stmt = $this->pdo->query($sql);

        $transacoes = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($dados['tipo'] === 'receita') {

                $transacao = new Receita(
                    (int) $dados['id'],
                    (float) $dados['valor'],
                    $dados['descricao'],
                    $dados['data']
                );

            } else {

                $transacao = new Despesa(
                    (int) $dados['id'],
                    (float) $dados['valor'],
                    $dados['descricao'],
                    $dados['data']
                );
            }

            $transacoes[] = $transacao;
        }

        return $transacoes;
    }
}