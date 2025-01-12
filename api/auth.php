<?php  
session_start();  
header('Content-Type: application/json');  
require_once '../config/database.php';  

try {  
    if (!isset($_POST['username']) || !isset($_POST['password'])) {  
        throw new Exception('Usuário e senha são obrigatórios');  
    }  

    $database = new Database();  
    $db = $database->getConnection();  

    $username = trim($_POST['username']);  
    $password = $_POST['password'];  

    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";  
    $stmt = $db->prepare($query);  
    $stmt->bindParam(':username', $username);  
    $stmt->execute();  

    if ($stmt->rowCount() === 0) {  
        throw new Exception('Usuário não encontrado');  
    }  

    $user = $stmt->fetch(PDO::FETCH_ASSOC);  

    if (!password_verify($password, $user['password'])) {  
        throw new Exception('Senha incorreta');  
    }  

    // Cria a sessão  
    $_SESSION['user'] = [  
        'id' => $user['id'],  
        'username' => $user['username'],  
        'nome_completo' => $user['nome_completo']  
    ];  

    echo json_encode([  
        'success' => true,  
        'message' => 'Login realizado com sucesso'  
    ]);  

} catch (Exception $e) {  
    http_response_code(400);  
    echo json_encode([  
        'success' => false,  
        'error' => $e->getMessage()  
    ]);  
}  
?>