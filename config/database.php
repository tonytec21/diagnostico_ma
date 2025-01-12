<?php  
class Database {  
    private $host = "localhost";  
    private $db_name = "u913401716_diagnostico";  
    private $username = "u913401716_diagnostico";  
    private $password = "@Rr6rh3264f9";  
    private $conn;  

    public function getConnection() {  
        try {  
            $this->conn = new PDO(  
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",  
                $this->username,  
                $this->password,  
                [  
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"  
                ]  
            );  
            return $this->conn;  
        } catch(PDOException $e) {  
            header('Content-Type: application/json');  
            http_response_code(500);  
            echo json_encode([  
                'success' => false,  
                'error' => 'Erro de conexão com o banco de dados',  
                'details' => $e->getMessage()  
            ]);  
            exit;  
        }  
    }  
}  
?>