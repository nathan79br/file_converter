let currentUploadedFile = "";

// Função para alternar entre as abas
function switchTab(tabName) {
    // Remove a classe active de todos os botões e conteúdos
    const allTabs = document.querySelectorAll('.nav-tab');
    const allContents = document.querySelectorAll('.tab-content');
    
    allTabs.forEach(tab => {
        tab.classList.remove('active');
    });
    
    allContents.forEach(content => {
        content.classList.remove('active');
    });
    
    // Adiciona a classe active ao botão e conteúdo clicados
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    const activeContent = document.getElementById(tabName);
    
    if (activeButton) {
        activeButton.classList.add('active');
    }
    
    if (activeContent) {
        activeContent.classList.add('active');
    }
}

// Função para abrir o seletor de arquivos
function triggerFileInput(type) {
    const fileInput = document.getElementById(`file-input-${type}`);
    if (fileInput) {
        fileInput.click();
    }
}

// Função para lidar com a seleção de arquivo
function handleFileSelect(event, mode) {

    document.getElementById("selected-file-name-converter").innerText = currentUploadedFile;
    document.getElementById("progress-bar-converter")
    document.getElementById("download-link-converter")

    document.getElementById("selected-file-name-compressor").innerText = currentUploadedFile;
    document.getElementById("progress-bar-compressor")
    document.getElementById("download-link-compressor")

    const file = event.target.files[0];
    if (!file) return;

    // Atualiza nome
    document.getElementById(`selected-file-name-${mode}`).textContent = file.name;

    // Mostra opções corretas
    if (mode === "converter") {
        document.getElementById("conversion-options").style.display = "block";
    } else {
        document.getElementById("compressor-options").style.display = "block";
    }

    // Upload
    const formData = new FormData();
    formData.append("file", file);

    fetch("./upload.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status !== "success") {
            alert("Erro no upload");
            console.error(data);
        } else {
            console.log("Upload OK:", data);
            currentUploadedFile = data.filename;

            if (mode === "converter") {
            generateConversionOptions(data.extension);
        }
        }
    })
    .catch(err => {
        console.error("Erro JS:", err);
    });
}

// Configurar drag and drop para as áreas de upload
document.addEventListener('DOMContentLoaded', function() {
    const uploadAreas = document.querySelectorAll('.upload-area');
    
    uploadAreas.forEach((area, index) => {
        const type = index === 0 ? 'converter' : 'compressor';
        
        // Prevenir comportamento padrão para drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            area.addEventListener(eventName, preventDefaults, false);
        });
        
        // Adicionar efeito visual quando arrastar arquivo sobre a área
        ['dragenter', 'dragover'].forEach(eventName => {
            area.addEventListener(eventName, () => {
                area.style.borderColor = 'rgba(3, 2, 19, 0.8)';
                area.style.backgroundColor = 'rgba(3, 2, 19, 0.02)';
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            area.addEventListener(eventName, () => {
                area.style.borderColor = '';
                area.style.backgroundColor = '';
            }, false);
        });
        
        // Lidar com o drop do arquivo
        area.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                const file = files[0];
                console.log(`Arquivo arrastado no ${type}:`, file.name);
                console.log('Tamanho:', (file.size / 1024 / 1024).toFixed(2) + ' MB');
                console.log('Tipo:', file.type);
                
                alert(`Arquivo "${file.name}" adicionado com sucesso!\n\nTamanho: ${(file.size / 1024 / 1024).toFixed(2)} MB\nTipo: ${file.type || 'Desconhecido'}`);
            }
        }, false);
    });
});

//Função que gera as opções de conversão
function generateConversionOptions(extension) {
    const select = document.getElementById("format-select");
    select.innerHTML = "";

    const formatsByType = {
        image: ["jpg", "png", "webp"],
        video: ["mp4", "webm"],
        audio: ["mp3", "wav", "ogg"],
        document: ["pdf"]
    };

    let group = [];

    if (["jpg", "jpeg", "png", "webp", "gif"].includes(extension)) {
        group = formatsByType.image;
    } else if (["mp4", "avi", "mov", "mkv"].includes(extension)) {
        group = formatsByType.video;
    } else if (["mp3", "wav", "ogg", "m4a"].includes(extension)) {
        group = formatsByType.audio;
    } else if (["doc", "docx", "ppt", "pptx", "xls", "xlsx"].includes(extension)) {
        group = formatsByType.document;
    }

    if (group.length === 0) {
        select.innerHTML = `<option value="">Nenhuma conversão disponível</option>`;
        return;
    }

    group.forEach(format => {
        const opt = document.createElement("option");
        opt.value = format;
        opt.textContent = format.toUpperCase();
        select.appendChild(opt);
    });
}

//Função que converte o arquivo
function convertFile() {

    const format = document.getElementById("format-select").value;

    const formData = new FormData();
    formData.append("filename", currentUploadedFile);
    formData.append("format", format);

    fetch("./convert.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {

            document.getElementById("progress-bar-converter").style.width = "80%";

            const downloadLink = document.getElementById("download-link-converter");
            downloadLink.href = data.download;
            downloadLink.style.display = "block";

        } else {
            console.log(data);
            alert(data.error);
        }

    });
}

//função que comprimi o arquivo
function compressFile() {
    const quality = document.getElementById("quality-select").value;

    if (!quality) {
        alert("Selecione a qualidade de compressão");
        return;
    }

    fetch("./compress.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            filename: currentUploadedFile,
            quality: quality
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert("Erro na compressão");
            console.error(data);
            return;
        }

        window.location.href = data.download;
    })
    .catch(err => console.error(err));
}

//função para trocar arquivo selecionado
function resetUpload() {

    document.getElementById("conversion-options").style.display = "none";
    document.querySelector("#converter .upload-area").style.display = "block";

    document.getElementById("format-select").innerHTML = "";
    document.getElementById("progress-bar").style.width = "0%";
    document.getElementById("download-link").style.display = "none";
}

// Função auxiliar para prevenir comportamentos padrão
function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

let currentFileExtension = "";
let currentFileName = "";

window.onload = loadHistory;
function loadHistory(){
    fetch("./api/history.php")
    .then(res => res.json())
    .then(data => {

        const container = document.getElementById("history-container");
        container.innerHTML = "";

        data.forEach(item => {
            const el = document.createElement("div");
            el.className = "history-item";
            el.innerHTML = `
                <div class="history-files">
                    <span>${item.original.split("/").pop()}</span>
                    →
                    <span>${item.converted.split("/").pop()}</span>
                </div>

                <div class="history-actions">
                    <button onclick="renameFile('${item.id}')">Renomear</button>
                    <button onclick="deleteFile('${item.id}')">Excluir</button>
                </div>`;

            container.appendChild(el);

        });
    });
}

function renameFile(id){
    const newName = prompt("Novo nome do arquivo:");

    if(!newName) return;

    fetch("./api/rename.php", {
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body: JSON.stringify({id,newName})
    })
    .then(res => res.json())
    .then(() => loadHistory());
}

function deleteFile(id){
    if(!confirm("Excluir arquivos?")) return;

    fetch("./api/delete.php", {
        method:"POST",
        headers:{
            "Content-Type":"application/json"
        },
        body: JSON.stringify({id})
    })
    .then(res => res.json())
    .then(() => loadHistory());
}