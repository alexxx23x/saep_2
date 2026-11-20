<?php
session_start();
require_once __DIR__ . '/conexao.php';

if (!empty($_SESSION['id_user'])) {
    header('Location: index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $st = $pdo->prepare("SELECT id_user, nome, login, senha, perfil FROM `user` WHERE login = ? AND ativo = 1");
    $st->execute([$login]);
    $usuario = $st->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);
        $_SESSION['id_user'] = (int) $usuario['id_user'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['perfil'];
        header('Location: index.php');
        exit;
    }
    $erro = 'Login ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Estoque SAEP</title><script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white rounded-xl shadow-sm p-8 w-full max-w-md">
<h1 class="text-2xl font-extrabold text-gray-800 mb-2">Estoque SAEP</h1>
<p class="text-sm text-gray-500 mb-6">Acesse o sistema com seu usuário.</p>
<?php if ($erro): ?><div class="bg-red-100 text-red-700 rounded-lg p-3 mb-4"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
<form method="post">
<label class="block text-sm font-semibold text-gray-600 mb-2">Login</label>
<input class="w-full border rounded-lg p-3 mb-4" name="login" required autofocus>
<label class="block text-sm font-semibold text-gray-600 mb-2">Senha</label>
<input class="w-full border rounded-lg p-3 mb-6" type="password" name="senha" required>
<button class="w-full bg-blue-700 text-white font-bold rounded-lg p-3">Entrar</button>
</form>

<p class="text-center text-sm text-gray-500 mt-5">
    Não possui uma conta?
    <a href="cadastro.php" class="text-blue-700 font-semibold">
        Cadastre-se
    </a>
</p>

</div></body></html>
