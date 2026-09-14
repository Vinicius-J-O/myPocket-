<?php

declare(strict_types=1);

class Meta
{
    private int $id;
    private string $nome;
    private float $valorObjetivo;
    private float $valorAtual;
    private string $dataLimite;

    public function __construct(
        int $id,
        string $nome,
        float $valorObjetivo,
        float $valorAtual,
        string $dataLimite
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->valorObjetivo = $valorObjetivo;
        $this->valorAtual = $valorAtual;
        $this->dataLimite = $dataLimite;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getValorObjetivo(): float
    {
        return $this->valorObjetivo;
    }

    public function getValorAtual(): float
    {
        return $this->valorAtual;
    }

    public function getDataLimite(): string
    {
        return $this->dataLimite;
    }

    public function getProgresso(): float
    {
        if ($this->valorObjetivo <= 0) {
            return 0;
        }

        return min(100, ($this->valorAtual / $this->valorObjetivo) * 100);
    }
}
