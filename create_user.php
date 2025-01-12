<?php  
session_start();  
// Verifica se o usuário está logado e se é admin (você pode ajustar essa lógica conforme necessário)  
if(!isset($_SESSION['user']) || $_SESSION['user']['username'] !== 'admin') {  
    header("Location: login.php");  
    exit;  
}  

require_once 'config/database.php';  

$message = '';  
$messageType = '';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    try {  
        $database = new Database();  
        $db = $database->getConnection();  

        // Validações  
        if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['nome_completo'])) {  
            throw new Exception('Todos os campos são obrigatórios');  
        }  

        // Verifica se o usuário já existe  
        $checkQuery = "SELECT id FROM users WHERE username = :username";  
        $checkStmt = $db->prepare($checkQuery);  
        $checkStmt->bindParam(':username', $_POST['username']);  
        $checkStmt->execute();  

        if ($checkStmt->rowCount() > 0) {  
            throw new Exception('Este nome de usuário já está em uso');  
        }  

        $username = trim($_POST['username']);  
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  
        $nome_completo = trim($_POST['nome_completo']);  

        $query = "INSERT INTO users (username, password, nome_completo)   
                  VALUES (:username, :password, :nome_completo)";  
        
        $stmt = $db->prepare($query);  
        $stmt->bindParam(':username', $username);  
        $stmt->bindParam(':password', $password);  
        $stmt->bindParam(':nome_completo', $nome_completo);  
        
        if($stmt->execute()) {  
            $message = "Usuário criado com sucesso!";  
            $messageType = "success";  
        } else {  
            throw new Exception("Erro ao criar usuário.");  
        }  

    } catch (Exception $e) {  
        $message = $e->getMessage();  
        $messageType = "danger";  
    }  
}  
?>  

<!DOCTYPE html>  
<html lang="pt-BR">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Cadastro de Usuário - Sistema de Diagnóstico MA</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">  
</head>  
<body class="bg-light">  
    <div class="container">  
        <div class="row justify-content-center mt-5">  
            <div class="col-md-6">  
                <div class="card shadow">  
                    <div class="card-body">  
                        <h2 class="text-center mb-4">  
                            <i class="fas fa-user-plus text-primary"></i>  
                            Cadastro de Usuário  
                        </h2>  

                        <?php if ($message): ?>  
                            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">  
                                <?php echo $message; ?>  
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>  
                            </div>  
                        <?php endif; ?>  

                        <form method="POST" action="" id="userForm">  
                            <div class="mb-3">  
                                <label for="username" class="form-label">Nome de Usuário</label>  
                                <input type="text" class="form-control" id="username" name="username" required  
                                       pattern="[a-zA-Z0-9_]{3,20}"   
                                       title="O nome de usuário deve ter entre 3 e 20 caracteres e pode conter apenas letras, números e underscore">  
                            </div>  

                            <div class="mb-3">  
                                <label for="password" class="form-label">Senha</label>  
                                <input type="password" class="form-control" id="password" name="password" required  
                                       minlength="6"  
                                       title="A senha deve ter no mínimo 6 caracteres">  
                            </div>  

                            <div class="mb-3">  
                                <label for="confirm_password" class="form-label">Confirmar Senha</label>  
                                <input type="password" class="form-control" id="confirm_password" required  
                                       minlength="6">  
                            </div>  

                            <div class="mb-4">  
                                <label for="nome_completo" class="form-label">Nome Completo</label>  
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>  
                            </div>  

                            <div class="d-grid gap-2">  
                                <button type="submit" class="btn btn-primary">  
                                    <i class="fas fa-save"></i> Cadastrar Usuário  
                                </button>  
                                <a href="index.php" class="btn btn-secondary">  
                                    <i class="fas fa-arrow-left"></i> Voltar  
                                </a>  
                            </div>  
                        </form>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>  
    <script>  
        document.getElementById('userForm').addEventListener('submit', function(e) {  
            const password = document.getElementById('password').value;  
            const confirmPassword = document.getElementById('confirm_password').value;  

            if (password !== confirmPassword) {  
                e.preventDefault();  
                alert('As senhas não coincidem!');  
                return false;  
            }  

            return true;  
        });  
    </script>  
</body>  
</html>