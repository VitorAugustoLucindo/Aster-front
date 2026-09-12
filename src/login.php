<?php
session_start();
require_once 'api_client.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firebase_token = $_POST['firebase_token'] ?? '';

    if (empty($firebase_token)) {
        $error = "Token do Firebase é obrigatório.";
    } else {
        $api = new ApiClient();
        try {
            // Calls POST /api/v1/auth/session as per api-spec.md
            $response = $api->post('/api/v1/auth/session', [
                'firebase_token' => $firebase_token
            ]);

            // Assuming the API returns the JWT in a field called 'token' or similar
            $jwt = $response['token'] ?? $response['jwt'] ?? null;

            if ($jwt) {
                $api->setToken($jwt);
                $success = "Autenticado com sucesso!";
                header("Refresh: 2; url=index.php");
            } else {
                $error = "Erro ao obter token de sessão.";
            }
        } catch (Exception $e) {
            $error = "Erro de autenticação: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Aster</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body p-4 text-center">
                        <h2 class="mb-4">Aster 📚</h2>
                        <p class="text-muted mb-4">Insira seu token do Firebase para acessar</p>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3 text-start">
                                <label class="form-label">Firebase Token</label>
                                <input type="text" name="firebase_token" class="form-control" required placeholder="eyJhbG... ">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                        <div class="mt-3">
                            <a href="index.php" class="text-decoration-none">Voltar para o catálogo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
