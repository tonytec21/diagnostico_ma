function editMunicipio(id) {  
    fetch(`api/get_municipio.php?id=${id}`)  
        .then(response => response.json())  
        .then(data => {  
            // Preencher o formulário com os dados existentes  
            document.getElementById('municipioId').value = data.id;  
            document.getElementById('nome').value = data.nome;  
            document.getElementById('numero_habitantes').value = data.numero_habitantes;  
            document.getElementById('quantidade_domicilios').value = data.quantidade_domicilios;  
            document.getElementById('nome_prefeito').value = data.nome_prefeito;  
            document.getElementById('situacao_politica').value = data.situacao_politica;  
            document.getElementById('quantidade_titulos_entregues').value = data.quantidade_titulos_entregues;  
            document.getElementById('secretaria_reg_fundiaria').checked = data.secretaria_reg_fundiaria == 1;  
            document.getElementById('empresa_reurb').checked = data.empresa_reurb == 1;  
            
            // Mudar o botão de submit para atualizar  
            const submitBtn = document.querySelector('#municipioForm button[type="submit"]');  
            submitBtn.textContent = 'Atualizar Município';  
            
            // Mudar a ação do formulário  
            document.getElementById('municipioForm').setAttribute('action', 'api/update_municipio.php');  
        })  
        .catch(error => console.error('Error:', error));  
}  

function deleteMunicipio(id) {  
    if(confirm('Tem certeza que deseja excluir este município?')) {  
        fetch('api/delete_municipio.php', {  
            method: 'POST',  
            body: JSON.stringify({id: id}),  
            headers: {  
                'Content-Type': 'application/json'  
            }  
        })  
        .then(response => response.json())  
        .then(data => {  
            if(data.success) {  
                alert('Município excluído com sucesso!');  
                loadMunicipios();  
            } else {  
                alert('Erro ao excluir município!');  
            }  
        })  
        .catch(error => console.error('Error:', error));  
    }  
}