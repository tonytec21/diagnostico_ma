<?php  
header('Content-Type: application/json');  
require_once '../config/database.php';  

try {  
    $database = new Database();  
    $db = $database->getConnection();  

    if (!$db) {  
        throw new Exception('Erro de conexão com o banco de dados');  
    }  

    $query = "SELECT * FROM municipios ORDER BY nome ASC";  
    $stmt = $db->prepare($query);  
    $stmt->execute();  

    $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);  

    // Converte os tipos de dados apropriadamente  
    foreach ($municipios as &$municipio) {  
        $municipio['numero_habitantes'] = intval($municipio['numero_habitantes']);  
        $municipio['quantidade_domicilios'] = intval($municipio['quantidade_domicilios']);  
        $municipio['quantidade_titulos_entregues'] = intval($municipio['quantidade_titulos_entregues']);  
        $municipio['secretaria_reg_fundiaria'] = (bool)$municipio['secretaria_reg_fundiaria'];  
        $municipio['empresa_reurb'] = (bool)$municipio['empresa_reurb'];  
    }  

    echo json_encode([  
        'success' => true,  
        'municipios' => $municipios  
    ]);  

} catch (Exception $e) {  
    http_response_code(500);  
    echo json_encode([  
        'success' => false,  
        'error' => 'Erro ao buscar municípios',  
        'details' => $e->getMessage()  
    ]);  
}  
?>