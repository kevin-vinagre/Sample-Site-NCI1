<?php
require_once __DIR__ . '/../../core/data/mysqlconn.php';

$errors = [];
$users = [];

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->query("SELECT id, username, email FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = 'Erro ao recuperar usuários: ' . $e->getMessage();
}

$template = file_get_contents(__DIR__ . '/view.html');

$rowsHtml = '';
foreach ($users as $u) {
    $rowsHtml .= '<tr>'
        . '<td>' . htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td>' . htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') . '</td>'
        . '<td>' . htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') . '</td>'
        . '</tr>';
}

$errorHtml = '';
if (!empty($errors)) {
    foreach ($errors as $err) {
        $errorHtml .= '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
    }
}

$template = str_replace('{{rows}}', $rowsHtml, $template);
$template = str_replace('{{errors}}', $errorHtml, $template);

echo $template;