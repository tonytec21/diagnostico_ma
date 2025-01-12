<?php  
session_start();  
if(!isset($_SESSION['user'])) {  
    header("Location: login.php");  
    exit;  
}  
?>
<!DOCTYPE html>  
<html lang="pt-BR">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <meta name="description" content="Sistema de Diagnóstico de Municípios do Maranhão">  
    <meta name="keywords" content="municípios, maranhão, diagnóstico, gestão municipal">  
    <meta name="author" content="Regular">  
    <meta name="theme-color" content="#2196F3">  
    <title>Diagnóstico Municípios MA</title>  
    
    <!-- Favicon -->  
    <link rel="icon" href="favicon_branco.png" type="image/x-icon">  
    
    <!-- CSS -->  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">  
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css" rel="stylesheet">  
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">  
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">  
    <link href="css/style.css" rel="stylesheet">  
    <style>  
/* Estilos adicionais para melhorar a aparência */  
.navbar {  
    padding: 0.8rem 1rem;  
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);  
}  

.navbar-brand {  
    font-size: 1.4rem;  
}  

.nav-link {  
    padding: 0.5rem 1rem;  
    transition: all 0.3s ease;  
}  

.nav-link:hover {  
    background-color: rgba(255, 255, 255, 0.1);  
    border-radius: 4px;  
}  

.dropdown-menu {  
    border: none;  
    margin-top: 0.5rem;  
}  

.dropdown-item {  
    padding: 0.7rem 1.5rem;  
}  

.dropdown-item:hover {  
    background-color: #f8f9fa;  
}  

@media (max-width: 992px) {  
    .navbar-collapse {  
        padding: 1rem 0;  
    }  
    
    .dropdown-menu {  
        border: none;  
        background-color: rgba(255, 255, 255, 0.1);  
    }  
    
    .dropdown-item {  
        color: white;  
    }  
    
    .dropdown-item:hover {  
        background-color: rgba(255, 255, 255, 0.2);  
        color: white;  
    }  
}  
</style>
</head>  
<body class="bg-light">  
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow">  
        <div class="container">  
            <!-- Logo e Nome do Sistema -->  
            <a class="navbar-brand d-flex align-items-center" href="#">  
                <i class="fas fa-map-marked-alt fa-lg me-2"></i>   
                <span class="fw-bold">Diagnóstico MA</span>  
            </a>  

            <!-- Toggle Button para Mobile -->  
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"   
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">  
                <span class="navbar-toggler-icon"></span>  
            </button>  

            <!-- Conteúdo da Navbar -->  
            <div class="collapse navbar-collapse" id="navbarContent">  
                <!-- Links de Navegação -->  
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">  
                    <li class="nav-item">  
                        <a class="nav-link active" href="#"><i class="fas fa-home"></i> Início</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i> Relatórios</a>  
                    </li>  
                    <li class="nav-item">  
                        <a class="nav-link" href="#"><i class="fas fa-cog"></i> Configurações</a>  
                    </li>  
                </ul>  

                <!-- Área do Usuário -->  
                <div class="d-flex align-items-center">  
                    <div class="dropdown me-3">  
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#"   
                        id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">  
                            <i class="fas fa-user-circle fa-lg me-2"></i>  
                            <span class="d-none d-md-inline">  
                                <?php echo htmlspecialchars($_SESSION['user']['nome_completo']); ?>  
                            </span>  
                        </a>  
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">  
                            <li>  
                                <a class="dropdown-item" href="#">  
                                    <i class="fas fa-user-cog me-2"></i> Perfil  
                                </a>  
                            </li>  
                            <li>  
                                <a class="dropdown-item" href="#">  
                                    <i class="fas fa-key me-2"></i> Alterar Senha  
                                </a>  
                            </li>  
                            <li><hr class="dropdown-divider"></li>  
                            <li>  
                                <a class="dropdown-item text-danger" href="api/logout.php">  
                                    <i class="fas fa-sign-out-alt me-2"></i> Sair  
                                </a>  
                            </li>  
                        </ul>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </nav>  
 

    <div class="container">  
        <!-- Card do Formulário -->  
        <div class="card shadow-sm mb-4 animate__animated animate__fadeIn">  
            <div class="card-header bg-white">  
                <h2 class="card-title mb-0">  
                    <i class="fas fa-plus-circle text-primary"></i>   
                    Cadastro de Município  
                </h2>  
            </div>  
            <div class="card-body">  
                <form id="municipioForm" method="POST">  
                    <div class="row">  
                        <div class="col-md-6 mb-3">  
                            <label for="nome" class="form-label">Nome do Município</label>  
                            <div class="input-group">  
                                <input type="text" class="form-control" id="nome" name="nome" required readonly>  
                                <input type="hidden" id="codigo_ibge" name="codigo_ibge">  
                                <input type="hidden" id="municipio_uf" name="municipio_uf"> 
                                <button class="btn btn-outline-primary" type="button" id="btnBuscarMunicipio">  
                                    <i class="fas fa-search"></i> Buscar  
                                </button>  
                            </div>  
                        </div>  
                        
                        <div class="col-md-6 mb-3">  
                            <label for="numero_habitantes" class="form-label">Número de Habitantes</label>  
                            <input type="number" class="form-control" id="numero_habitantes" name="numero_habitantes" required>  
                        </div>  
                        
                        <div class="col-md-6 mb-3">  
                            <label for="quantidade_domicilios" class="form-label">Quantidade de Domicílios</label>  
                            <input type="number" class="form-control" id="quantidade_domicilios" name="quantidade_domicilios" required>  
                        </div>  
                        
                        <div class="col-md-6 mb-3">  
                            <label for="nome_prefeito" class="form-label">Nome do Prefeito</label>  
                            <input type="text" class="form-control" id="nome_prefeito" name="nome_prefeito" required>  
                        </div>  
                        
                        <div class="col-md-4 mb-3">  
                            <label for="situacao_politica" class="form-label">Situação Política</label>  
                            <select class="form-select" id="situacao_politica" name="situacao_politica" required>  
                                <option value="">Selecione...</option>  
                                <option value="eleito">Eleito</option>  
                                <option value="reeleito">Reeleito</option>  
                                <option value="sucessor">Sucessor</option>  
                            </select>  
                        </div>  
                        
                        <div class="col-md-4 mb-3">  
                            <label for="quantidade_titulos_entregues" class="form-label">Quantidade de Títulos Entregues</label>  
                            <input type="number" class="form-control" id="quantidade_titulos_entregues" name="quantidade_titulos_entregues" required>  
                        </div>  
                        
                        <div class="col-md-4 mb-3">  
                            <label class="form-label">Características</label>  
                            <div class="form-check">  
                                <input class="form-check-input" type="checkbox" id="secretaria_reg_fundiaria" name="secretaria_reg_fundiaria" value="1">  
                                <label class="form-check-label" for="secretaria_reg_fundiaria">  
                                    Possui Secretaria de Regularização Fundiária  
                                </label>  
                            </div>  
                            
                            <div class="form-check">  
                                <input class="form-check-input" type="checkbox" id="empresa_reurb" name="empresa_reurb" value="1">  
                                <label class="form-check-label" for="empresa_reurb">  
                                    Possui Empresa de REURB  
                                </label>  
                            </div>  
                        </div>  
                    </div>  
                    
                    <div class="text-center">  
                        <button type="submit" class="btn btn-primary">  
                            <i class="fas fa-save"></i> Cadastrar Município  
                        </button>  
                        <button type="reset" class="btn btn-secondary">  
                            <i class="fas fa-eraser"></i> Limpar  
                        </button>  
                    </div>  
                </form>  
            </div>  
        </div>  
        <!-- Card da Tabela -->  
        <div class="card shadow-sm mb-4 animate__animated animate__fadeIn">  
            <div class="card-header bg-white d-flex justify-content-between align-items-center">  
                <h2 class="card-title mb-0">  
                    <i class="fas fa-table text-primary"></i>   
                    Municípios Cadastrados  
                </h2>  
                <div class="input-group w-auto">  
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar município...">  
                    <button class="btn btn-outline-primary" type="button" onclick="searchMunicipios()">  
                        <i class="fas fa-search"></i>  
                    </button>  
                </div>  
            </div>  
            <div class="card-body">  
                <div class="table-responsive">  
                    <table class="table table-hover">  
                        <thead class="table-light">  
                            <tr>  
                                <th>Município</th>  
                                <th>Habitantes</th>  
                                <th>Domicílios</th>  
                                <th>Prefeito</th>
                                <th>Situação Política</th>  
                                <th>Títulos</th>  
                                <th>Sec de REURB</th>  
                                <th>Empresa de REURB</th>  
                                <th>Ações</th>  
                            </tr> 
                        </thead>  
                        <tbody id="municipiosTableBody"></tbody>  
                    </table>  
                </div>  
            </div>  
        </div>  

        <!-- Cards dos Gráficos -->  
        <div class="row">  
            <div class="col-md-6 mb-4">  
                <div class="card shadow-sm animate__animated animate__fadeIn">  
                    <div class="card-header bg-white">  
                        <h2 class="card-title mb-0">  
                            <i class="fas fa-chart-bar text-primary"></i>   
                            Títulos Entregues  
                        </h2>  
                    </div>  
                    <div class="card-body">  
                        <canvas id="titulosChart"></canvas>  
                    </div>  
                </div>  
            </div>  
            <div class="col-md-6 mb-4">  
                <div class="card shadow-sm animate__animated animate__fadeIn">  
                    <div class="card-header bg-white">  
                        <h2 class="card-title mb-0">  
                            <i class="fas fa-chart-bar text-primary"></i>   
                            População  
                        </h2>  
                    </div>  
                    <div class="card-body">  
                        <canvas id="habitantesChart"></canvas>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  

    <!-- Modal de Busca de Municípios -->  
    <div class="modal fade" id="municipioModal" tabindex="-1">  
        <div class="modal-dialog modal-lg">  
            <div class="modal-content">  
                <div class="modal-header">  
                    <h5 class="modal-title">  
                        <i class="fas fa-search text-primary"></i>   
                        Buscar Município  
                    </h5>  
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>  
                </div>  
                <div class="modal-body">  
                    <div class="input-group mb-3">  
                        <input type="text" id="searchMunicipio" class="form-control"   
                               placeholder="Digite o nome do município...">  
                        <button class="btn btn-primary" type="button" id="btnSearchMunicipio">  
                            <i class="fas fa-search"></i> Buscar  
                        </button>  
                    </div>  
                    <div class="table-responsive">  
                        <table class="table table-hover">  
                            <thead class="table-light">  
                                <tr>  
                                    <th>Município</th>  
                                    <th>Código IBGE</th>  
                                    <th>UF</th>  
                                    <th>Ação</th>  
                                </tr>  
                            </thead>  
                            <tbody id="municipiosResults"></tbody>  
                        </table>  
                    </div>  
                </div>  
            </div>  
        </div>  
    </div>  

    <!-- Scripts -->  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>  
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  
    <script src="js/main.js"></script>  
</body>  
</html>