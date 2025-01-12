<?php  
header('Content-Type: application/json');  
require_once '../config/database.php';  

try {  
    $database = new Database();  
    $db = $database->getConnection();  

    // Verifica se o ID foi fornecido  
    if (!isset($_GET['id'])) {  
        throw new Exception('ID não fornecido');  
    }  

    $id = intval($_GET['id']);  

    // Prepara e executa a consulta  
    $query = "SELECT * FROM municipios WHERE id = :id LIMIT 1";  
    $stmt = $db->prepare($query);  
    $stmt->bindParam(':id', $id);  
    $stmt->execute();  

    // Verifica se encontrou o município  
    if ($stmt->rowCount() === 0) {  
        throw new Exception('Município não encontrado');  
    }  

    // Obtém os dados do município  
    $municipio = $stmt->fetch(PDO::FETCH_ASSOC);  

    // Retorna os dados em formato JSON  
    echo json_encode([  
        'success' => true,  
        'municipio' => $municipio  
    ]);  

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