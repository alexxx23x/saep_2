<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function exigir_login(): void {
    if (empty($_SESSION['id_user'])) {
        header('Location: login.php');
        exit;
    }
}

function exigir_admin(): void {
    exigir_login();
    if ($_SESSION['perfil'] !== 'ADMINISTRADOR') {
        http_response_code(403);
        exit('Acesso permitido somente ao administrador. <a href="index.php">Voltar</a>');
    }
}

function eh_admin(): bool {
    return ($_SESSION['perfil'] ?? '') === 'ADMINISTRADOR';
}
