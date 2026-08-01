<?php
declare(strict_types=1);

session_start();

require_once 'classes/Transacao.php';
require_once 'classes/Receita.php';
require_once 'classes/Despesa.php';
require_once 'classes/Carteira.php';

if (!isset($_SESSION['carteira'])) {
    $_SESSION['carteira'] = serialize(new Carteira());
}

$carteira = unserialize($_SESSION['carteira']);

try {

    $tipo = $_POST['tipo'];
    $valor = (float) $_POST['valor'];
    $descricao = trim($_POST['descricao']);
    $data = $_POST['data'];

    if ($tipo === 'receita') {

        $receita = new Receita(
            $valor,
            $descricao,
            $data
        );

        $carteira->adicionarReceita($receita);

    } else {

        $despesa = new Despesa(
            $valor,
            $descricao,
            $data
        );

        $carteira->adicionarDespesa($despesa);
    }

    $_SESSION['mensagem'] = 'Transação cadastrada com sucesso!';

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();
}

$_SESSION['carteira'] = serialize($carteira);

header('Location: index.php');
exit;