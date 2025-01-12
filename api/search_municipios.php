<?php  
header("Content-Type: application/json; charset=UTF-8");  
require_once '../config/database.php';  

$database = new Database();  
$db = $database->getConnection();  

try {  
    $searchTerm = isset($_GET['term']) ? $_GET['term'] : '';  
    
    $sql = "SELECT * FROM municipios   
            WHERE nome LIKE :term   
            OR nome_prefeito LIKE :term   
            ORDER BY nome";  
            
    $stmt = $db->prepare($sql);  
    $searchTerm = "%{$searchTerm}%";  
    $stmt->bindParam(':term', $searchTerm);  
    $stmt->execute();  
    
    $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);  
    echo json_encode($municipios);  
} catch(PDOException $e) {  
    echo json_encode(['error' => $e->getMessage()]);  
}  
?>