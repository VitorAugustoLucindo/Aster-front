<?php
session_start();
require_once 'api_client.php';

$api = new ApiClient();
$books = [];
$error = null;

try {
    // Attempt to fetch books from the backend
    $response = $api->get('/api/v1/books'); 
    // Note: Adjusting endpoint based on actual Flask routes
    $books = $response['books'] ?? [];
} catch (Exception $e) {
    $error = "Erro ao carregar livros: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Aster - Catálogo Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Aster 📚</a>
            <div class="d-flex">
                <a href="create_book.php" class="btn btn-outline-light">Enviar Livro</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <h2 class="mb-4">Catálogo de Livros</h2>
        
        <div class="row">
            <?php if (empty($books)): ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Nenhum livro encontrado no catálogo.</p>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="card-text text-muted">
                                    <?= htmlspecialchars($book['author'] ?? 'Autor desconhecido') ?>
                                </p>
                                <a href="book.php?id=<?= $book['id'] ?>" class="btn btn-primary btn-sm">Ver Detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
