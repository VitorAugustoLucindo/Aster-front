<?php
session_start();
require_once 'api_client.php';

$api = new ApiClient();
$error = null;
$success = null;

// Check if user is authenticated
if (!isset($_SESSION['aster_jwt'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => $_POST['title'] ?? '',
        'author' => $_POST['author'] ?? '',
        'description' => $_POST['description'] ?? '',
        'isbn' => $_POST['isbn'] ?? null,
        'published_year' => $_POST['published_year'] ?? null,
    ];

    if (empty($data['title']) || empty($data['author'])) {
        $error = "Título e Autor são obrigatórios.";
    } else {
        try {
            $api->post('/api/v1/books', $data);
            $success = "Livro cadastrado com sucesso!";
        } catch (Exception $e) {
            $error = "Erro ao cadastrar livro: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Enviar Livro - Aster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Aster 📚</a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-light small">Autenticado</span>
                <a href="logout.php" class="btn btn-sm btn-outline-light">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="mb-4">Cadastrar Novo Livro</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Autor *</label>
                                <input type="text" name="author" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ISBN</label>
                                <input type="text" name="isbn" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ano de Publicação</label>
                                <input type="number" name="published_year" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Salvar Livro</button>
                                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
