<?php  
header('Content-Type: application/json');  
require_once '../config/database.php';  

try {  
    $database = new Database();  
    $db = $database->getConnection();  

    // Verifica o método da requisição  
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {  
        throw new Exception('Método não permitido');  
    }  

    // Obter ID da URL  
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;  
    
    if (!$id) {  
        throw new Exception('ID não fornecido');  
    }  

    // Verifica se o município existe  
    $checkQuery = "SELECT id FROM municipios WHERE id = :id";  
    $checkStmt = $db->prepare($checkQuery);  
    $checkStmt->bindParam(':id', $id);  
    $checkStmt->execute();  

    if ($checkStmt->rowCount() === 0) {  
        throw new Exception('Município não encontrado');  
    }  

    // Executa a exclusão  
    $query = "DELETE FROM municipios WHERE id = :id";  
    $stmt = $db->prepare($query);  
    $stmt->bindParam(':id', $id);  
    $success = $stmt->execute();  

    if ($success) {  
        echo json_encode([  
            'success' => true,  
            'message' => 'Município excluído com sucesso'  
        ]);  
    } else {  
        throw new Exception('Erro ao excluir município');  
    }  

} catch (Exception $e) {  
    http_response_code(400);  
    echo json_encode([  
        'success' => false,  
        'error' => $e->getMessage()  
    ]);  
} catch (PDOException $e) {  
    http_response_code(500);  
    echo json_encode([  
        'success' => false,  
        'error' => 'Erro no banco de dados',  
        'details' => $e->getMessage()  
    ]);  
}  
?>