<?php  
session_start();  
if(isset($_SESSION['user'])) {  
    header("Location: index.php");  
    exit;  
}  
?>  
<!DOCTYPE html>  
<html lang="pt-BR">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Login - Sistema de Diagnóstico MA</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">  
    <link href="css/style.css" rel="stylesheet">  
    <link rel="icon" href="favicon_branco.png" type="image/x-icon">  
</head>  
<body class="bg-light">  
    <div class="container">  
        <div class="row justify-content-center min-vh-100 align-items-center">  
            <div class="col-md-4">  
                <div class="card shadow-sm animate__animated animate__fadeIn">  
                    <div class="card-body p-4">  
                        <h2 class="text-center mb-4">  
                            <i class="fas fa-user-lock text-primary"></i>  
                            Login  
                        </h2>  
                        
                        <form id="loginForm" action="api/auth.php" method="POST">  
                            <div class="mb-3">  
                                <label for="username" class="form-label">Usuário</label>  
                                <input type="text" class="form-control" id="username" name="username" required>  
                            </div>  
                            
                            <div class="mb-4">  
                                <label for="password" class="form-label">Senha</label>  
                                <input type="password" class="form-control" id="password" name="password" required>  
                            </div>  
                            
                            <button type="submit" class="btn btn-primary w-100">  
                                <i class="fas fa-sign-in-alt"></i> Entrar  
                            </button>  
                        </form>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
    <script>  
        document.getElementById('loginForm').addEventListener('submit', async function(e) {  
            e.preventDefault();  
            
            try {  
                const formData = new FormData(this);  
                const response = await fetch('api/auth.php', {  
                    method: 'POST',  
                    body: formData  
                });  
                
                const data = await response.json();  
                
                if (data.success) {  
                    window.location.href = 'index.php';  
                } else {  
                    throw new Error(data.error);  
                }  
            } catch (error) {  
                Swal.fire({  
                    icon: 'error',  
                    title: 'Erro',  
                    text: error.message || 'Erro ao fazer login'  
                });  
            }  
        });  
    </script>  
</body>  
</html>