
<?php
session_start();
require_once __DIR__ . '/conexao.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    if ($nome === '' || $login === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } elseif (strlen($senha) < 6) {
        $erro = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $erro = 'As senhas não são iguais.';
    } else {
        $stmt = $pdo->prepare("SELECT id_user FROM `user` WHERE login = ?");
        $stmt->execute([$login]);

        if ($stmt->fetch()) {
            $erro = 'Este login já está cadastrado.';
        } else {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO `user` (nome, login, senha, perfil, ativo)
                 VALUES (?, ?, ?, 'OPERADOR', 1)"
            );

            $stmt->execute([$nome, $login, $senhaHash]);

            $sucesso = 'Cadastro realizado com sucesso! Você já pode fazer login.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Estoque SAEP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

<div class="bg-white rounded-xl shadow-sm p-8 w-full max-w-md">

    <h1 class="text-2xl font-extrabold text-gray-800 mb-2">
        Estoque SAEP
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Crie sua conta para acessar o sistema.
    </p>

    <?php if ($erro): ?>
        <div class="bg-red-100 text-red-700 rounded-lg p-3 mb-4">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <div class="bg-green-100 text-green-700 rounded-lg p-3 mb-4">
            <?= htmlspecialchars($sucesso) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label class="block text-sm font-semibold text-gray-600 mb-2">
            Nome completo
        </label>
        <input type="text" name="nome" required maxlength="100"
               class="w-full border rounded-lg p-3 mb-4">

        <label class="block text-sm font-semibold text-gray-600 mb-2">
            Login
        </label>
        <input type="text" name="login" required maxlength="50"
               class="w-full border rounded-lg p-3 mb-4">

        <label class="block text-sm font-semibold text-gray-600 mb-2">
            Senha
        </label>
        <input type="password" name="senha" required
               class="w-full border rounded-lg p-3 mb-4">

        <label class="block text-sm font-semibold text-gray-600 mb-2">
            Confirmar senha
        </label>
        <input type="password" name="confirmar_senha" required
               class="w-full border rounded-lg p-3 mb-6">

        <button type="submit"
                class="w-full bg-blue-700 text-white font-bold rounded-lg p-3">
            Criar conta
        </button>

    </form>

    <p class="text-center text-sm text-gray-500 mt-5">
        Já possui uma conta?
        <a href="login.php" class="text-blue-700 font-semibold">
            Fazer login
        </a>
    </p>

</div>

</body>
</html>
