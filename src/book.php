<?php
session_start();
require_once 'api_client.php';

$api = new ApiClient();
$book = null;
$error = null;

$book_id = $_GET['id'] ?? null;

if (!$book_id) {
    header("Location: index.php");
    exit;
}

try {
    $book = $api->get("/api/v1/books/$book_id");
} catch (Exception $e) {
    $error = "Erro ao carregar livro: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $book ? htmlspecialchars($book['title']) : 'Livro não encontrado' ?> - Aster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Aster 📚</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        <?php elseif ($book): ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-body p-5">
                            <h1 class="display-5"><?= htmlspecialchars($book['title']) ?></h1>
                            <h3 class="text-muted mb-4"><?= htmlspecialchars($book['author'] ?? 'Autor desconhecido') ?></h3>
                            <hr>
                            <div class="mt-4">
                                <h5>Descrição</h5>
                                <p class="lead"><?= nl2br(htmlspecialchars($book['description'] ?? 'Sem descrição disponível.')) ?></p>
                            </div>
                            
                            <div class="mt-4 d-flex gap-2">
                                <a href="index.php" class="btn btn-outline-secondary">Voltar ao Catálogo</a>
                                <?php if (isset($_SESSION['aster_jwt'])): ?>
                                    <a href="edit_book.php?id=<?= $book['id'] ?>" class="btn btn-warning">Editar Livro</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Livro não encontrado.</div>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        <?php endif; ?>
    </div>
</body>
</html>
