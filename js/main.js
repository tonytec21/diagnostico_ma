// Variáveis globais  
const form = document.getElementById('municipioForm');  
const searchInput = document.getElementById('searchInput');  
const municipioModal = new bootstrap.Modal(document.getElementById('municipioModal'));  
let charts = {};  

// Event Listeners  
document.addEventListener('DOMContentLoaded', initialize);  
form.addEventListener('submit', handleFormSubmit);  
document.getElementById('btnBuscarMunicipio').addEventListener('click', showMunicipioModal);  
document.getElementById('btnSearchMunicipio').addEventListener('click', searchMunicipiosIBGE);  
searchInput.addEventListener('input', searchMunicipios);  

// Inicialização  
async function initialize() {  
    initializeCharts();  // Primeiro inicializa os gráficos  
    await loadMunicipios(); // Depois carrega os municípios  
    await updateCharts();   // Por fim, atualiza os gráficos com os dados  
}  

// Funções de manipulação do formulário  
async function handleFormSubmit(e) {  
    e.preventDefault();  
    
    try {  
        const formData = new FormData(form);  
        const isEdit = form.dataset.id;  
        
        // Trata os checkboxes especificamente  
        formData.set('secretaria_reg_fundiaria',   
            document.getElementById('secretaria_reg_fundiaria').checked ? '1' : '0');  
        formData.set('empresa_reurb',   
            document.getElementById('empresa_reurb').checked ? '1' : '0');  
        
        if (isEdit) {  
            formData.append('id', form.dataset.id);  
        }  
        
        const url = isEdit ? 'api/update_municipio.php' : 'api/create_municipio.php';  
        
        const response = await fetch(url, {  
            method: 'POST',  
            body: formData  
        });  
        
        const data = await response.json();  
        
        if (data.success) {  
            showAlert('Sucesso',   
                     isEdit ? 'Município atualizado com sucesso!' : 'Município cadastrado com sucesso!',   
                     'success');  
            
            resetForm();  
            await loadMunicipios();  
            await updateCharts();  
        } else {  
            throw new Error(data.error || 'Erro ao processar município');  
        }  
    } catch (error) {  
        showAlert('Erro', error.message, 'error');  
        console.error('Error:', error);  
    }  
}

function resetForm() {  
    form.reset();  
    delete form.dataset.id;  
    form.querySelector('button[type="submit"]').textContent = 'Cadastrar Município';  
    document.getElementById('btnBuscarMunicipio').disabled = false;  
}
// Funções de busca e carregamento  
async function loadMunicipios() {  
    try {  
        const response = await fetch('api/get_municipios.php');  
        const data = await response.json();  
        
        if (!data.success) {  
            throw new Error(data.error || 'Erro ao carregar municípios');  
        }  
        
        updateTable(data.municipios);  
        
    } catch (error) {  
        showAlert('Erro', error.message, 'error');  
        console.error('Error:', error);  
    }  
}  

async function searchMunicipiosIBGE() {  
    const searchTerm = document.getElementById('searchMunicipio').value.trim();  
    
    if (searchTerm.length < 3) {  
        showAlert('Aviso', 'Digite pelo menos 3 caracteres para pesquisar', 'warning');  
        return;  
    }  
    
    try {  
        const response = await fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/municipios?q=${searchTerm}`);  
        const municipios = await response.json();  
        
        const results = document.getElementById('municipiosResults');  
        results.innerHTML = '';  
        
        municipios  
            .filter(m => m.microrregiao.mesorregiao.UF.sigla === 'MA')  
            .forEach(m => {  
                const tr = document.createElement('tr');  
                tr.innerHTML = `  
                    <td>${m.nome}</td>  
                    <td>${m.id}</td>  
                    <td>${m.microrregiao.mesorregiao.UF.sigla}</td>  
                    <td>  
                        <button class="btn btn-sm btn-primary" onclick="selectMunicipio('${m.nome}', '${m.id}', '${m.microrregiao.mesorregiao.UF.sigla}')">  
                            <i class="fas fa-check"></i> Selecionar  
                        </button>  
                    </td>  
                `;  
                results.appendChild(tr);  
            });  
            
    } catch (error) {  
        showAlert('Erro', 'Erro ao buscar municípios do IBGE', 'error');  
        console.error('Error:', error);  
    }  
}  

function selectMunicipio(nome, codigo, uf) {  
    document.getElementById('nome').value = nome;  
    document.getElementById('codigo_ibge').value = codigo;  
    document.getElementById('municipio_uf').value = uf;  
    municipioModal.hide();  
}
// Funções de edição e exclusão  
async function editMunicipio(id) {  
    try {  
        const response = await fetch(`api/get_municipio.php?id=${id}`);  
        
        if (!response.ok) {  
            throw new Error(`HTTP error! status: ${response.status}`);  
        }  
        
        const data = await response.json();  
        
        if (!data.success) {  
            throw new Error(data.error || 'Erro ao carregar dados do município');  
        }  
        
        const municipio = data.municipio;  
        
        // Reseta o formulário antes de preencher  
        form.reset();  
        
        // Preenche os campos  
        Object.keys(municipio).forEach(key => {  
            const element = document.getElementById(key);  
            if (element) {  
                if (element.type === 'checkbox') {  
                    element.checked = municipio[key] == 1;  
                } else {  
                    element.value = municipio[key];  
                }  
            }  
        });  

        // Garante que o campo UF seja preenchido corretamente  
        document.getElementById('municipio_uf').value = municipio.uf;  
        
        // Configura o modo de edição  
        form.dataset.id = id;  
        form.querySelector('button[type="submit"]').textContent = 'Atualizar Município';  
        document.getElementById('btnBuscarMunicipio').disabled = true;  
        
        // Scroll para o formulário  
        form.scrollIntoView({ behavior: 'smooth' });  
        
    } catch (error) {  
        showAlert('Erro', 'Erro ao carregar dados do município: ' + error.message, 'error');  
        console.error('Error:', error);  
    }  
}  

async function deleteMunicipio(id) {  
    try {  
        const result = await Swal.fire({  
            title: 'Confirmar exclusão',  
            text: 'Tem certeza que deseja excluir este município?',  
            icon: 'warning',  
            showCancelButton: true,  
            confirmButtonColor: '#d33',  
            cancelButtonColor: '#3085d6',  
            confirmButtonText: 'Sim, excluir!',  
            cancelButtonText: 'Cancelar'  
        });  
        
        if (result.isConfirmed) {  
            const response = await fetch(`api/delete_municipio.php?id=${id}`, {  
                method: 'DELETE'  
            });  
            
            const data = await response.json();  
            
            if (data.success) {  
                showAlert('Sucesso', 'Município excluído com sucesso!', 'success');  
                await loadMunicipios();  
                await updateCharts();  
            } else {  
                throw new Error(data.error || 'Erro ao excluir município');  
            }  
        }  
    } catch (error) {  
        showAlert('Erro', error.message, 'error');  
        console.error('Error:', error);  
    }  
}
// Funções de atualização da interface  
function updateTable(municipios) {  
    const tbody = document.getElementById('municipiosTableBody');  
    tbody.innerHTML = '';  
    
    municipios.forEach(municipio => {  
        const tr = document.createElement('tr');  
        tr.innerHTML = `  
            <td>${municipio.nome}/${municipio.uf}</td>  
            <td>${parseInt(municipio.numero_habitantes).toLocaleString()}</td>  
            <td>${parseInt(municipio.quantidade_domicilios).toLocaleString()}</td>  
            <td>${municipio.nome_prefeito}</td>  
            <td>${municipio.situacao_politica}</td>  
            <td>${parseInt(municipio.quantidade_titulos_entregues).toLocaleString()}</td>  
            <td>${municipio.secretaria_reg_fundiaria == 1 ? 'Sim' : 'Não'}</td>  
            <td>${municipio.empresa_reurb == 1 ? 'Sim' : 'Não'}</td>  
            <td>  
                <button class="btn btn-sm btn-primary" onclick="editMunicipio(${municipio.id})">  
                    <i class="fas fa-edit"></i>  
                </button>  
                <button class="btn btn-sm btn-danger" onclick="deleteMunicipio(${municipio.id})">  
                    <i class="fas fa-trash"></i>  
                </button>  
            </td>  
        `;  
        tbody.appendChild(tr);  
    });   
}  

function searchMunicipios() {  
    const searchTerm = searchInput.value.toLowerCase();  
    const rows = document.querySelectorAll('#municipiosTableBody tr');  
    
    rows.forEach(row => {  
        const text = row.textContent.toLowerCase();  
        row.style.display = text.includes(searchTerm) ? '' : 'none';  
    });  
}
// Funções de gráficos  
function initializeCharts() {  
    // Gráfico de Títulos  
    const titulosCtx = document.getElementById('titulosChart').getContext('2d');  
    charts.titulos = new Chart(titulosCtx, {  
        type: 'bar',  
        data: {  
            labels: [],  
            datasets: [{  
                label: 'Títulos Entregues',  
                data: [],  
                backgroundColor: 'rgba(33, 150, 243, 0.5)',  
                borderColor: 'rgba(33, 150, 243, 1)',  
                borderWidth: 1  
            }]  
        },  
        options: {  
            responsive: true,  
            maintainAspectRatio: false,  
            plugins: {  
                legend: {  
                    position: 'top',  
                }  
            },  
            scales: {  
                y: {  
                    beginAtZero: true,  
                    ticks: {  
                        callback: function(value) {  
                            return value.toLocaleString();  
                        }  
                    }  
                }  
            }  
        }  
    });  
    
    // Gráfico de Habitantes  
    const habitantesCtx = document.getElementById('habitantesChart').getContext('2d');  
    charts.habitantes = new Chart(habitantesCtx, {  
        type: 'bar',  
        data: {  
            labels: [],  
            datasets: [{  
                label: 'Número de Habitantes',  
                data: [],  
                backgroundColor: 'rgba(76, 175, 80, 0.5)',  
                borderColor: 'rgba(76, 175, 80, 1)',  
                borderWidth: 1  
            }]  
        },  
        options: {  
            responsive: true,  
            maintainAspectRatio: false,  
            plugins: {  
                legend: {  
                    position: 'top',  
                }  
            },  
            scales: {  
                y: {  
                    beginAtZero: true,  
                    ticks: {  
                        callback: function(value) {  
                            return value.toLocaleString();  
                        }  
                    }  
                }  
            }  
        }  
    });  
}  

async function updateCharts() {  
    try {  
        const response = await fetch('api/get_municipios.php');  
        const data = await response.json();  
        
        if (!data.success) {  
            throw new Error(data.error || 'Erro ao carregar dados para os gráficos');  
        }  
        
        const municipios = data.municipios;  
        
        // Ordena os municípios por quantidade de títulos (top 10)  
        const municipiosOrdenados = [...municipios]  
            .sort((a, b) => b.quantidade_titulos_entregues - a.quantidade_titulos_entregues)  
            .slice(0, 10);  
        
        // Atualiza o gráfico de títulos  
        charts.titulos.data.labels = municipiosOrdenados.map(m => m.nome);  
        charts.titulos.data.datasets[0].data = municipiosOrdenados.map(m =>   
            parseInt(m.quantidade_titulos_entregues));  
        charts.titulos.update();  
        
        // Ordena os municípios por número de habitantes (top 10)  
        const municipiosPorHabitantes = [...municipios]  
            .sort((a, b) => b.numero_habitantes - a.numero_habitantes)  
            .slice(0, 10);  
        
        // Atualiza o gráfico de habitantes  
        charts.habitantes.data.labels = municipiosPorHabitantes.map(m => m.nome);  
        charts.habitantes.data.datasets[0].data = municipiosPorHabitantes.map(m =>   
            parseInt(m.numero_habitantes));  
        charts.habitantes.update();  
        
    } catch (error) {  
        console.error('Erro ao atualizar gráficos:', error);  
        showAlert('Erro', 'Erro ao atualizar gráficos: ' + error.message, 'error');  
    }  
}  

// Função auxiliar para mostrar alertas  
function showAlert(title, message, icon) {  
    Swal.fire({  
        title: title,  
        text: message,  
        icon: icon,  
        confirmButtonColor: '#2196F3'  
    });  
}  

// Função auxiliar para mostrar o modal de busca  
function showMunicipioModal() {  
    document.getElementById('searchMunicipio').value = '';  
    document.getElementById('municipiosResults').innerHTML = '';  
    municipioModal.show();  
}  

// Adiciona CSS para os gráficos  
document.addEventListener('DOMContentLoaded', function() {  
    const style = document.createElement('style');  
    style.textContent = `  
        .card-body canvas {  
            min-height: 300px;  
        }  
    `;  
    document.head.appendChild(style);  
});  

// Exporta funções necessárias para uso global  
window.selectMunicipio = selectMunicipio;  
window.editMunicipio = editMunicipio;  
window.deleteMunicipio = deleteMunicipio;