<?php  
session_start();  
header('Content-Type: application/json');  
require_once '../config/database.php';  

// Verifica se usuário está logado  
if (!isset($_SESSION['user'])) {  
    http_response_code(401);  
    echo json_encode([  
        'success' => false,  
        'error' => 'Usuário não autenticado'  
    ]);  
    exit;  
}  

try {  
    $database = new Database();  
    $db = $database->getConnection();  

    // Validação dos campos obrigatórios  
    $requiredFields = [  
        'nome',   
        'codigo_ibge',   
        'municipio_uf',   
        'numero_habitantes',   
        'quantidade_domicilios',   
        'nome_prefeito',   
        'situacao_politica',   
        'quantidade_titulos_entregues'  
    ];  
                      
    foreach ($requiredFields as $field) {  
        if (!isset($_POST[$field]) || empty($_POST[$field])) {  
            throw new Exception("Campo obrigatório não preenchido: $field");  
        }  
    }  

    // Validações adicionais  
    if (!is_numeric($_POST['numero_habitantes']) || $_POST['numero_habitantes'] <= 0) {  
        throw new Exception("Número de habitantes inválido");  
    }  

    if (!is_numeric($_POST['quantidade_domicilios']) || $_POST['quantidade_domicilios'] <= 0) {  
        throw new Exception("Quantidade de domicílios inválida");  
    }  

    if (!is_numeric($_POST['quantidade_titulos_entregues']) || $_POST['quantidade_titulos_entregues'] < 0) {  
        throw new Exception("Quantidade de títulos entregues inválida");  
    }  

    // Prepara os dados para inserção  
    $data = [  
        'nome' => trim($_POST['nome']),  
        'codigo_ibge' => trim($_POST['codigo_ibge']),  
        'uf' => trim($_POST['municipio_uf']),  
        'numero_habitantes' => intval($_POST['numero_habitantes']),  
        'quantidade_domicilios' => intval($_POST['quantidade_domicilios']),  
        'nome_prefeito' => trim($_POST['nome_prefeito']),  
        'situacao_politica' => trim($_POST['situacao_politica']),  
        'quantidade_titulos_entregues' => intval($_POST['quantidade_titulos_entregues']), 
        'secretaria_reg_fundiaria' => isset($_POST['secretaria_reg_fundiaria']) && $_POST['secretaria_reg_fundiaria'] === '1' ? 1 : 0,  
        'empresa_reurb' => isset($_POST['empresa_reurb']) && $_POST['empresa_reurb'] === '1' ? 1 : 0,     
        'usuario_cadastro' => $_SESSION['user']['username'],  
        'data_cadastro' => date('Y-m-d H:i:s')  
    ];  

    // Verifica se já existe um município com o mesmo código IBGE  
    $checkQuery = "SELECT id FROM municipios WHERE codigo_ibge = :codigo_ibge";  
    $checkStmt = $db->prepare($checkQuery);  
    $checkStmt->bindParam(':codigo_ibge', $data['codigo_ibge']);  
    $checkStmt->execute();  

    if ($checkStmt->rowCount() > 0) {  
        throw new Exception("Já existe um município cadastrado com este código IBGE");  
    }  

    // Query de inserção  
    $sql = "INSERT INTO municipios (  
                nome,   
                codigo_ibge,   
                uf,   
                numero_habitantes,   
                quantidade_domicilios,  
                nome_prefeito,   
                situacao_politica,   
                quantidade_titulos_entregues,  
                secretaria_reg_fundiaria,   
                empresa_reurb,   
                usuario_cadastro,  
                data_cadastro  
            ) VALUES (  
                :nome,   
                :codigo_ibge,   
                :uf,   
                :numero_habitantes,   
                :quantidade_domicilios,  
                :nome_prefeito,   
                :situacao_politica,   
                :quantidade_titulos_entregues,  
                :secretaria_reg_fundiaria,   
                :empresa_reurb,   
                :usuario_cadastro,  
                :data_cadastro  
            )";  

    $stmt = $db->prepare($sql);  
    $success = $stmt->execute($data);  

    if ($success) {  
        // Log da ação  
        $logQuery = "INSERT INTO logs (  
            acao,   
            usuario,   
            descricao  
        ) VALUES (  
            'CREATE',   
            :usuario,   
            :descricao  
        )";  
        
        $logStmt = $db->prepare($logQuery);  
        $logStmt->execute([  
            'usuario' => $_SESSION['user']['username'],  
            'descricao' => "Cadastro do município: {$data['nome']}/{$data['uf']}"  
        ]);  

        echo json_encode([  
            'success' => true,  
            'message' => 'Município cadastrado com sucesso',  
            'id' => $db->lastInsertId()  
        ]);  
    } else {  
        throw new Exception('Erro ao cadastrar município');  
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