<?php

declare(strict_types=1);

abstract class Transacao
{
    protected int $id;
    protected float $valor;
    protected string $descricao;
    protected string $data;

    public function __construct(
        int $id,
        float $valor,
        string $descricao,
        string $data
    ) {
        $this->id = $id;
        $this->valor = $valor;
        $this->descricao = $descricao;
        $this->data = $data;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValor(): float
    {
        return $this->valor;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getData(): string
    {
        return $this->data;
    }

    abstract public function getTipo(): string;
}