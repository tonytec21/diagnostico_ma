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
    if (!isset($_POST['id'])) {  
        throw new Exception('ID do município não fornecido');  
    }  

    $database = new Database();  
    $db = $database->getConnection();  

    // Validação dos campos obrigatórios  
    $requiredFields = [  
        'nome',   
        'codigo_ibge',   
        'municipio_uf',  // Alterado de 'uf' para 'municipio_uf'  
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

    // Prepara os dados para atualização  
    $data = [  
        'id' => $_POST['id'],  
        'nome' => trim($_POST['nome']),  
        'codigo_ibge' => trim($_POST['codigo_ibge']),  
        'uf' => trim($_POST['municipio_uf']),  // Alterado para usar municipio_uf  
        'numero_habitantes' => intval($_POST['numero_habitantes']),  
        'quantidade_domicilios' => intval($_POST['quantidade_domicilios']),  
        'nome_prefeito' => trim($_POST['nome_prefeito']),  
        'situacao_politica' => trim($_POST['situacao_politica']),  
        'quantidade_titulos_entregues' => intval($_POST['quantidade_titulos_entregues']),  
        'secretaria_reg_fundiaria' => isset($_POST['secretaria_reg_fundiaria']) && $_POST['secretaria_reg_fundiaria'] === '1' ? 1 : 0,  
        'empresa_reurb' => isset($_POST['empresa_reurb']) && $_POST['empresa_reurb'] === '1' ? 1 : 0,     
        'usuario_atualizacao' => $_SESSION['user']['username']  
    ];  

    // Verifica se já existe outro município com o mesmo código IBGE  
    $checkQuery = "SELECT id FROM municipios WHERE codigo_ibge = :codigo_ibge AND id != :id";  
    $checkStmt = $db->prepare($checkQuery);  
    $checkStmt->bindParam(':codigo_ibge', $data['codigo_ibge']);  
    $checkStmt->bindParam(':id', $data['id']);  
    $checkStmt->execute();  

    if ($checkStmt->rowCount() > 0) {  
        throw new Exception("Já existe outro município cadastrado com este código IBGE");  
    }  

    // Query de atualização  
    $sql = "UPDATE municipios SET   
                nome = :nome,  
                codigo_ibge = :codigo_ibge,  
                uf = :uf,  
                numero_habitantes = :numero_habitantes,  
                quantidade_domicilios = :quantidade_domicilios,  
                nome_prefeito = :nome_prefeito,  
                situacao_politica = :situacao_politica,  
                quantidade_titulos_entregues = :quantidade_titulos_entregues,  
                secretaria_reg_fundiaria = :secretaria_reg_fundiaria,  
                empresa_reurb = :empresa_reurb,  
                usuario_atualizacao = :usuario_atualizacao,  
                data_atualizacao = NOW()  
            WHERE id = :id";  

    $stmt = $db->prepare($sql);  
    $success = $stmt->execute($data);  

    if ($success) {  
        // Log da ação  
        $logQuery = "INSERT INTO logs (  
            acao,  
            usuario,  
            descricao  
        ) VALUES (  
            'UPDATE',  
            :usuario,  
            :descricao  
        )";  
        
        $logStmt = $db->prepare($logQuery);  
        $logStmt->execute([  
            'usuario' => $_SESSION['user']['username'],  
            'descricao' => "Atualização do município: {$data['nome']}/{$data['uf']}"  
        ]);  

        echo json_encode([  
            'success' => true,  
            'message' => 'Município atualizado com sucesso'  
        ]);  
    } else {  
        throw new Exception('Erro ao atualizar município');  
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