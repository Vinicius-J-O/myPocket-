<?php

declare(strict_types=1);

session_start();

require_once 'conexao.php';

try {
    $nome = trim($_POST['nome'] ?? '');
    $sobrenome = trim($_POST['sobrenome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($nome === '') {
        throw new Exception('Informe o nome.');
    }

    if ($sobrenome === '') {
        throw new Exception('Informe o sobrenome.');
    }

    if ($email === '') {
        throw new Exception('Informe o email.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Informe um email válido.');
    }

    if ($login === '') {
        throw new Exception('Informe o seu nome de usuário.');
    }

    if ($senha === '') {
        throw new Exception('Informe a senha.');
    }

    if (strlen($senha) < 6) {
        throw new Exception('A senha deve ter pelo menos 6 caracteres.');
    }

    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuario
        (nome, sobrenome, email, login, senha)
        VALUES
        (:nome, :sobrenome, :email, :login, :senha)
    ");

    $stmt->execute([
        ':nome' => $nome,
        ':sobrenome' => $sobrenome,
        ':email' => $email,
        ':login' => $login,
        ':senha' => $senhaHash
    ]);

    $_SESSION['mensagem'] = 'Usuário cadastrado com sucesso.';

} catch (PDOException $e) {

    if ($e->getCode() === '23000') {
        $_SESSION['erro'] = 'O email ou nome de usuário já está cadastrado.';
    } else {
        $_SESSION['erro'] = 'Erro ao cadastrar o usuário.';
    }

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();
}

header('Location: index.php');
exit;