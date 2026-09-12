<?php
session_start();
require_once 'api_client.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_token = $_POST['id_token'] ?? '';

    if (empty($id_token)) {
        $error = "Token de autenticação do Google é obrigatório.";
    } else {
        $api = new ApiClient();
        try {
            // Agora enviamos o id_token do Google para o backend
            $response = $api->post('/api/v1/auth/session', [
                'id_token' => $id_token
            ]);

            $jwt = $response['token'] ?? $response['jwt'] ?? null;

            if ($jwt) {
                $api->setToken($jwt);
                $success = "Autenticado com sucesso!";
                header("Refresh: 2; url=index.php");
            } else {
                $error = "Erro ao obter token de sessão do servidor.";
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
    <!-- Google Identity Services SDK -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body p-4 text-center">
                        <h2 class="mb-4">Aster 📚</h2>
                        <p class="text-muted mb-4">Acesse sua conta para gerenciar seu catálogo</p>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>

                        <!-- Botão do Google Identity Services -->
                        <div id="g_id_onload"
                             data-client_id="SEU_GOOGLE_CLIENT_ID.apps.googleusercontent.com"
                             data-context="signin"
                             data-ux_mode="popup"
                             data-callback="handleCredentialResponse"
                             data-auto_prompt="false">
                        </div>

                        <div class="g_id_signin"
                             data-type="standard"
                             data-shape="rectangular"
                             data-theme="outline"
                             data-text="signin_with"
                             data-size="large"
                             data-logo_alignment="left">
                        </div>

                        <!-- Formulário oculto para enviar o token ao PHP -->
                        <form id="google-token-form" method="POST" style="display: none;">
                            <input type="hidden" name="id_token" id="id_token">
                        </form>

                        <div class="mt-4">
                            <a href="index.php" class="text-decoration-none">Voltar para o catálogo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleCredentialResponse(response) {
            // O response.credential é o JWT (ID Token) assinado pelo Google
            document.getElementById('id_token').value = response.credential;
            document.getElementById('google-token-form').submit();
        }
    </script>
</body>
</html>
