<?php

require_once __DIR__.'/../../core/data/mysqlconn.php';

$errors = [];
$data = [
    'username' => '',
    'email' => '',
    'pass' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['username'] = trim($_POST['username'] ?? '');
    $data['email'] = trim($_POST['email'] ?? '');
    $data['pass'] = trim($_POST['pass'] ?? '');

    if ($data['username'] === '') {
        $errors[] = 'Nome de usuário é obrigatório';
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email válido é obrigatório';
    }

    if ($data['pass'] === '') {
        $errors[] = 'Senha é obrigatória';
    }

    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();

            $query = "CALL add_user_with_points(:username,:email,:pass,:ammount)";
            $stmt = $pdo->prepare($query);

            $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':pass' => password_hash($data['pass'],PASSWORD_DEFAULT),
                ':ammount' => 45.0
            ]);

            echo 'Cadastro realizado com sucesso!';
            exit;
        } catch(PDOException $e) {
            $errors[] = 'Erro ao cadastrar: ' . $e->getMessage();
        }
    }
}

$template = file_get_contents(__DIR__ . '/view.html');

$nameValue = htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8');
$emailValue = htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8');
$passValue = htmlspecialchars($data['pass'], ENT_QUOTES, 'UTF-8');

$errorHtml = '';
if (!empty($errors)) {
    foreach ($errors as $error) {
        $errorHtml .= '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
    }
}

$template = str_replace('{{username}}', $nameValue, $template);
$template = str_replace('{{email}}', $emailValue, $template);
$template = str_replace('{{pass}}', $passValue, $template);
$template = str_replace('{{errors}}', $errorHtml, $template);

echo $template;
