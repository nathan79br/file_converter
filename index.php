<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversor e Compressor de Arquivos</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <!-- Barra de Navegação -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-tabs">
                <button class="nav-tab active" data-tab="converter" onclick="switchTab('converter')"> 
                    Conversor                  
                    <div class="tab-indicator"></div>
                </button>

                <button class="nav-tab" data-tab="compressor" onclick="switchTab('compressor')">
                    Compressor
                    <div class="tab-indicator"></div>
                </button>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        <div class="content-container">
            <div id="converter" class="tab-content active">
                <h1 class="title">Conversor de Arquivos</h1>
                <p class="description">
                    Converta seus arquivos entre diferentes formatos de forma rápida e fácil. 
                    Suporta imagens, documentos, áudio e vídeo. Basta fazer o upload do arquivo 
                    e escolher o formato de saída desejado.
                </p>

                <!-- Área de Upload -->
                <div class="upload-area" onclick="triggerFileInput('converter')">
                    <div class="upload-content">
                        <div class="upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="upload-title">Faça upload do seu arquivo</h3>
                            <p class="upload-text">Arraste e solte ou clique no botão abaixo</p>
                        </div>
                        <button type="button" class="upload-button" onclick="event.stopPropagation(); triggerFileInput('converter')">
                            Selecionar Arquivo
                        </button>
                    </div>
                </div>
                <input type="file" id="file-input-converter" class="file-input" onchange="handleFileSelect(event, 'converter')">

                <div id="conversion-options" style="display:none; margin-top:20px;">
                    <p><b>Arquivo Inserido</b></p>
                    <h3 id="selected-file-name-converter"></h3>
                   
                    <div style="margin-top:15px;">
                        <select class="conversion-box" id="format-select"></select>
                        <button type="button" class="secondary-btn" onclick="resetUpload()">Trocar Arquivo</button>
                        <button type="button" class="primary-btn" onclick="convertFile()">Converter</button>
                    </div>

                    <div style="margin-top:20px; width:100%; background:#eee; height:10px;">
                        <div id="progress-bar-converter" style="width:0%; height:100%; background:#030213;"></div>
                    </div>

                    <a class="download-btn" id="download-link-converter" style="display:none; margin-top:15px;" download>
                        Baixar Arquivo Convertido
                    </a>
                </div>
            </div>

            <div id="compressor" class="tab-content">
                <h1 class="title">Compressor de Arquivos</h1>
                <p class="description">
                    Reduza o tamanho dos seus arquivos sem perder qualidade. Ideal para otimizar 
                    imagens, vídeos e documentos, economizando espaço de armazenamento e 
                    facilitando o compartilhamento.
                </p>

                <div id="compressor-options" style="display:none; margin-top:20px;">
                    <p><b>Arquivo Inserido</b></p>
                    <h3 id="selected-file-name-compressor"></h3>
                   
                    <div style="margin-top:15px;">
                        <select id="quality-select">
                            <option value="max">Qualidade máxima (quase sem perda)</option>
                            <option value="high">Alta qualidade (leve compressão)</option>
                            <option value="balanced" selected>Equilíbrio (até 10% de perda)</option>
                        </select>
                        <button type="button" class="secondary-btn" onclick="resetUpload()">Trocar Arquivo</button>
                        <button type="button" class="primary-btn" onclick="compressFile()">Comprimir</button>
                    </div>

                    <div style="margin-top:20px; width:100%; background:#eee; height:10px;">
                        <div id="progress-bar-compressor" style="width:0%; height:100%; background:#030213;"></div>
                    </div>

                    <a class="download-btn" id="download-link-compressor" style="display:none; margin-top:15px;" download>
                        Baixar Arquivo Convertido
                    </a>
                </div>

                <!-- Área de Upload -->
                <div class="upload-area" onclick="triggerFileInput('compressor')">
                    <div class="upload-content">
                        <div class="upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <div>
                            <h3 class="upload-title">Faça upload do seu arquivo</h3>
                            <p class="upload-text">Arraste e solte ou clique no botão abaixo</p>
                        </div>
                        <button type="button" class="upload-button" onclick="event.stopPropagation(); triggerFileInput('compressor')">
                            Selecionar Arquivo
                        </button>
                    </div>
                </div>
                <input type="file" id="file-input-compressor" class="file-input" onchange="handleFileSelect(event, 'compressor')">
            </div>

            </div>
                
        </div>


        <section class="history-section">

        <h2>Histórico de Arquivos</h2>

        <div id="history-container">
            <div class="history-item">
                <div class="history-files">
                    <div class="history-original">
                        <p>Original</p>
                        <span>01.png</span>
                    </div>

                    <div class="history-arrow">➡</div>

                    <div class="history-converted">
                        <p>Convertido</p>
                        <span>01.jpeg</span>
                    </div>
                </div>

                <div class="history-actions">
                    <button class="rename-btn">Renomear</button>
                    <button class="delete-btn">Excluir</button>
                </div>

            </div>


        </div>

        </section>

    </main>

   <script src="assets/js/app.js"></script>
</body>
</html>